<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends BaseController
{
    public function searchBook(Request $request)
    {
        $query = $request->query();
        if (!array_key_exists('q', $query) || !$query['q']) {
            return $this->responseSuccess([], 'OK');
        }
        try {
            $searchKey = $this->fullTextWildcards($query['q']);
            $searchResult = $this->getSearchResult($searchKey);
        } catch (\Exception $ex) {
            return $this->responseError('', 'Internal Server Error', 500, 500);
        }

        return $this->responseSuccess(BookResource::collection($searchResult), 'OK');
    }

    protected function getSearchResult(string $query)
    {
        $bookResult = DB::table('books')
            ->join('publishers', 'books.publisher_id', '=', 'publishers.id')
            ->join('author_book', 'books.id', '=', 'author_book.book_id')
            ->join('authors', 'author_book.author_id', '=', 'authors.id')
            ->whereRaw("ts_title || ' ' || ts_summary @@ to_tsquery('english', ?)", [$query])
            ->orWhereRaw("publishers.name @@ to_tsquery('english', ?)", [$query])
            ->orWhereRaw("authors.name @@ to_tsquery('english', ?)", [$query])
            ->select('books.*', 'publishers.name as publisher_name', 'publishers.id as publisher_id')
            ->limit(20)
            ->get();

        $bookIds = $bookResult->pluck('id');

        $authors = DB::table('author_book')
            ->whereIn('book_id', $bookIds)
            ->join('authors', 'author_book.author_id', '=', 'authors.id')
            ->select('book_id', 'authors.id', 'authors.name')
            ->get();

        $authorsByBookId = $authors->groupBy('book_id');

        return $bookResult->map(function ($book) use ($authorsByBookId) {
            $bookAuthors = $authorsByBookId->get($book->id);

            $book->authors = $bookAuthors ? $bookAuthors->map(function ($bookAuthor) {
                return [
                    'id' => $bookAuthor->id,
                    'name' => $bookAuthor->name
                ];
            }) : [];
            $book->publisher = [
                'id' => $book->publisher_id,
                'name' => $book->publisher_name
            ];
            return $book;
        });
    }

    protected function fullTextWildcards($term): string
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            if (strlen($word) >= 1) {
                $words[$key] = '+' . $word  . '*';
            }
        }

        return implode('', $words);
    }
}
