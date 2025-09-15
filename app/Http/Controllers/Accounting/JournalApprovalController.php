<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Journal;
use App\Services\Accounting\PostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JournalApprovalController extends Controller
{
    public function __construct(private PostingService $postingService)
    {
        $this->middleware(['auth', 'permission:journals.approve']);
    }

    public function index()
    {
        return view('journals.approval.index');
    }

    public function show(int $id)
    {
        $journal = Journal::with([
            'lines.account',
            'lines.project',
            'lines.fund',
            'lines.department'
        ])->findOrFail($id);

        if (!$journal->isDraft()) {
            return redirect()->route('journals.approval.index')->with('error', 'Only draft journals can be reviewed for approval.');
        }

        return view('journals.approval.show', compact('journal'));
    }

    public function approve(Request $request, int $id)
    {
        $journal = Journal::findOrFail($id);
        if (!$journal->isDraft()) {
            return back()->with('error', 'Only draft journals can be approved.');
        }

        try {
            $this->postingService->postDraftJournal($journal->id, $request->user()->id);
            return redirect()->route('journals.approval.index')->with('success', "Journal #{$journal->journal_no} approved and posted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving journal: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        $query = Journal::with('lines')
            ->where('status', 'draft')
            ->select('id', 'journal_no', 'date', 'description', 'status');

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->input('to'));
        }
        if ($request->filled('desc')) {
            $query->where('description', 'like', '%' . $request->input('desc') . '%');
        }

        return DataTables::of($query)
            ->addColumn('total_debit', function (Journal $journal) {
                return number_format($journal->lines->sum('debit'), 2);
            })
            ->addColumn('total_credit', function (Journal $journal) {
                return number_format($journal->lines->sum('credit'), 2);
            })
            ->addColumn('actions', function (Journal $journal) {
                $viewUrl = route('journals.approval.show', $journal->id);
                $approveUrl = route('journals.approval.approve', $journal->id);
                return '<a href="' . $viewUrl . '" class="btn btn-info btn-xs mr-1"><i class="fas fa-eye"></i> View</a>' .
                    '<button type="button" class="btn btn-success btn-xs approve-button" data-id="' . $journal->id . '" data-url="' . $approveUrl . '"><i class="fas fa-check"></i> Approve</button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}
