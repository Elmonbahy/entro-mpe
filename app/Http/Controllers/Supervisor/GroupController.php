<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Supervisor\GroupExport;
use Maatwebsite\Excel\Facades\Excel;

class GroupController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.group.supervisor.index');
  }

  public function exportExcel()
  {
    $fileName = 'group' . '.xlsx';
    return Excel::download(new GroupExport, $fileName);
  }
}
