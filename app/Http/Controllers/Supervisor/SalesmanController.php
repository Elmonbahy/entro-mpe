<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesmanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.salesman.supervisor.index');
  }
}
