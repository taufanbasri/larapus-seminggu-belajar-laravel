<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BorrowLog;
use Yajra\DataTables\Html\Builder;
use DataTables;

class StatisticsController extends Controller
{
  public function index(Request $request, Builder $htmlBuilder)
  {
    if ($request->ajax()) {
      $stats = BorrowLog::with('book','user');
      if ($request->get('status') == 'returned') $stats->returned();
      if ($request->get('status') == 'not-returned') $stats->borrowed();

      return Datatables::of($stats)
      ->addColumn('returned_at', function($stat){
        if ($stat->is_returned) {
          return $stat->updated_at;
        }
        return "Masih dipinjam";
      })->toJson();
    }

  $html = $htmlBuilder->columns([
    ['data' => 'book.title', 'name' => 'book.title', 'title' => 'Judul'],
    ['data' => 'user.name', 'name' => 'user.name', 'title' => 'Peminjam'],
    ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Tanggal Pinjam', 'searchable' => false],
    ['data' => 'returned_at', 'name' => 'returned_at', 'title' => 'Tanggal Kembali', 'orderable' => false, 'searchable' => false]
  ]);

  return view('statistics.index')->with(compact('html'));
  }
}
