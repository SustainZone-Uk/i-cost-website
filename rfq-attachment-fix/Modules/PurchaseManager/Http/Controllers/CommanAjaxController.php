<?php

namespace Modules\PurchaseManager\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Project;
use Modules\PurchaseManager\Entities\Purchase;
use Modules\PurchaseManager\Entities\Quotation;
use Modules\PurchaseManager\Notifications\QuotationReplyReceived;
use Modules\EstimateManager\Entities\SubActivity;
use Modules\EstimateManager\Entities\MainActivity;
use Yajra\DataTables\Utilities\Request as DatatableRequest;


class CommanAjaxController extends Controller
{
    /**
     * RFQ list and unread supplier replies for the current user's accessible projects.
     */
    public function getQuotations(DatatableRequest $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $user = auth()->user();

        $query = Quotation::select([
            'quotations.id',
            'quotations.user_id',
            'quotations.project_id',
            'quotations.notes',
            'quotations.delivery_date',
            'quotations.delivery_address',
        ])->with(['user', 'project']);

        if (!$user->isRole('Super Admin')) {
            if ($user->isRole('Admin')) {
                $query->whereHas('project', function ($projectQuery) use ($user) {
                    $projectQuery->where('company_id', $user->id);
                });
            } elseif ($user->isRole('Supplier')) {
                $query->whereHas('suppliers', function ($supplierQuery) use ($user) {
                    $supplierQuery->where('users.id', $user->id);
                });
            } else {
                $query->whereHas('project', function ($projectQuery) use ($user) {
                    $projectQuery->where('company_id', $user->company_id);
                })->whereExists(function ($assignmentQuery) use ($user) {
                    $assignmentQuery->select(DB::raw('1'))
                        ->from('users_project')
                        ->whereColumn('users_project.project_id', 'quotations.project_id')
                        ->where('users_project.users_id', $user->id);
                });
            }
        }

        // Use the same notification source as the sidebar and the RFQ read action.
        $unreadIds = collect(QuotationReplyReceived::unreadQuotationIds($user))
            ->filter(function ($id) {
                return is_numeric($id) && (int) $id > 0;
            })
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        $unreadLookup = array_fill_keys($unreadIds, true);

        // Build this before project filtering and table pagination so an unread
        // RFQ is still identifiable when it belongs to another accessible project.
        $unreadRfqs = [];
        if (!empty($unreadIds)) {
            $unreadQuery = clone $query;
            $unreadRfqs = $unreadQuery
                ->whereIn('quotations.id', $unreadIds)
                ->orderBy('quotations.id', 'desc')
                ->get()
                ->map(function ($quotation) {
                    return [
                        'id' => (int) $quotation->id,
                        'rfq' => $this->quotationReference($quotation),
                        'project_id' => (int) $quotation->project_id,
                        'project_name' => (string) optional($quotation->project)->project_title,
                        'view_url' => route('quotations.view', ['id' => $quotation->id]),
                    ];
                })
                ->values()
                ->all();
        }

        $query->when($request->project_filter_id, function ($quotationQuery) use ($request) {
            $quotationQuery->where('quotations.project_id', $request->project_filter_id);
        });

        return datatables()->of($query)
            ->addColumn('name', function ($quotation) {
                return (string) optional($quotation->user)->full_name;
            })
            ->addColumn('rfq', function ($quotation) {
                return $this->quotationReference($quotation);
            })
            ->addColumn('new_reply', function ($quotation) use ($unreadLookup) {
                // Explicit 1/0 also remains usable if DataTables serializes it as text.
                return isset($unreadLookup[(int) $quotation->id]) ? 1 : 0;
            })
            ->addColumn('action', function ($quotation) use ($user, $unreadLookup) {
                $hasNewReply = isset($unreadLookup[(int) $quotation->id]);
                $viewTitle = $hasNewReply
                    ? 'Open RFQ and read new supplier reply'
                    : 'View RFQ';

                $actions = '<a href="' .
                    e(route('quotations.view', ['id' => $quotation->id])) .
                    '" class="btn btn-primary btn-sm" title="' . e($viewTitle) .
                    '" aria-label="' . e($viewTitle) .
                    '"><i class="fas fa-eye" aria-hidden="true"></i></a>';

                if ($user->can('access', 'purchase orders add')) {
                    $actions .= '&nbsp;<a href="' .
                        e(route('quotations.edit', ['id' => $quotation->id])) .
                        '" class="btn btn-info btn-sm" title="Edit RFQ" aria-label="Edit RFQ">' .
                        '<i class="fas fa-pencil-alt" aria-hidden="true"></i></a>';

                    $actions .= '&nbsp;<a href="' .
                        e(route('quotations.send.quotation', ['id' => $quotation->id])) .
                        '" class="btn btn-info btn-sm" title="Send RFQ" aria-label="Send RFQ">' .
                        '<i class="fas fa-paper-plane" aria-hidden="true"></i></a>';
                }

                return $actions;
            })
            ->with([
                'unread_rfqs' => $unreadRfqs,
                'unread_rfq_count' => count($unreadRfqs),
            ])
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Preserve the existing project-prefix RFQ numbering.
     */
    private function quotationReference($quotation)
    {
        $title = trim((string) optional($quotation->project)->project_title);
        $words = preg_split('/\\s+/', $title, -1, PREG_SPLIT_NO_EMPTY);
        $prefix = '';

        if (count($words) > 1) {
            foreach ($words as $word) {
                $prefix .= substr($word, 0, 1);
            }
        } else {
            $prefix = substr($title, 0, 3);
        }

        if ($prefix === '') {
            $prefix = 'RFQ';
        }

        return $prefix . '-' . str_pad($quotation->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get quotation from.
     * @param Request $request
     * @return Response
     */
	 public function getPurchaseOrders(DatatableRequest $request) {

        if($request->ajax()){

          $query =DB::table('v_purchase_delivery_certificate_supplier')->select(['id', 'project_id', 'supplier_id', 'revision_no', 'purchase_no', 'grand_total', 'delivery_date', 'created_at','supplier_name','unique_reference_no','company_id']);
            if(!auth()->user()->isRole('Super Admin')){
                if(auth()->user()->isRole('Admin')){

                    $query->where('company_id', auth()->id());
                }else if(auth()->user()->isRole('Supplier')){
                    $query->where('supplier_id', auth()->id());
                }else{
					$query =DB::table('v_purchase_delivery_certificate_supplier')
					->join('users_project','users_project.project_id', '=', 'v_purchase_delivery_certificate_supplier.project_id')
					->select(['v_purchase_delivery_certificate_supplier.id AS id', 'v_purchase_delivery_certificate_supplier.project_id AS project_id', 'v_purchase_delivery_certificate_supplier.supplier_id AS supplier_id', 'v_purchase_delivery_certificate_supplier.revision_no AS revision_no', 'v_purchase_delivery_certificate_supplier.purchase_no AS purchase_no', 'v_purchase_delivery_certificate_supplier.grand_total AS grand_total', 'v_purchase_delivery_certificate_supplier.delivery_date AS delivery_date', 'v_purchase_delivery_certificate_supplier.created_at AS created_at','v_purchase_delivery_certificate_supplier.supplier_name AS supplier_name','v_purchase_delivery_certificate_supplier.unique_reference_no AS unique_reference_no','v_purchase_delivery_certificate_supplier.company_id AS company_id'])
					->where([
					    ['v_purchase_delivery_certificate_supplier.company_id', auth()->user()->company_id],
						['users_project.users_id', auth()->id()]
					   ])
					   ->distinct();

					/*$query =DB::table('purchases')
					->join('projects','projects.id', '=', 'purchases.project_id')
					->join('users_project','users_project.project_id', '=', 'purchases.project_id')
					->join('users','users.id', '=', 'purchases.supplier_id')
					->select('purchases.id', 'purchases.project_id', 'purchases.supplier_id AS supplier_id', 'purchases.revision_no AS revision_no', 'purchases.purchase_no As purchase_no', 'purchases.grand_total AS grand_total', 'purchases.delivery_date As delivery_date', 'purchases.created_at AS created_at','users.supplier_name AS supplier_name','projects.unique_reference_no AS unique_reference_no')
					->where([
					    ['projects.company_id', auth()->user()->company_id],
						['users_project.users_id', auth()->id()]
					   ]);
                    $query->whereHas('project', function($q){
                        $q->where('company_id', auth()->user()->company_id);
                    });*/
                }
            }
            if((!auth()->user()->isRole('Admin')) && (!auth()->user()->isRole('Super Admin')) && (!auth()->user()->isRole('Supplier'))){
            $query->when($request->project_filter_id, function($q) use($request){
                $q->where('v_purchase_delivery_certificate_supplier.project_id', $request->project_filter_id);
            });
            $query->when($request->delivery_date, function($q) use($request){
                $q->whereDate('v_purchase_delivery_certificate_supplier.delivery_date', $request->delivery_date);
            });
            }else{
				$query->when($request->project_filter_id, function($q) use($request){
                $q->where('project_id', $request->project_filter_id);
            });
            $query->when($request->delivery_date, function($q) use($request){
                $q->whereDate('delivery_date', $request->delivery_date);
            });
			}
            return datatables()->of($query)
                    ->addColumn('supplier', function ($purchase) {

						 return @$purchase->supplier_name;

                    })
                   ->editColumn('revision_no', function ($purchase) {
                        $html = "<select onchange='location = this.value;'>";
                        $html .= "<option value='' disabled selected>Rev ".$purchase->revision_no."</option>";
                        for($rn = 1; $rn <= $purchase->revision_no; $rn++){
                            $html .= "<option value='".route('purchase.orders.history', ['id'=>$purchase->id, 'revision_no'=>$rn])."'>".$rn."</option>";
                        }
                        $html .= "</select>";
                        return $html;
                    })
                   ->editColumn('purchase_no', function ($purchase) {

							 return $purchase->unique_reference_no;



                    })
                     ->editColumn('grand_total', function ($purchase) {

                        return '&pound;'.round($purchase->grand_total,2);
                    })
                    ->editColumn('created_at', function ($purchase) {
                        return $purchase->created_at;
                    })
                    ->editColumn('delivery_date', function ($purchase) {

						  return $purchase->delivery_date;

                    })
                    ->editColumn('invoice_no', function ($purchase) {
                        $invoices_po = DB::table('purchase_invoices')->select('*')->where('purchase_id',$purchase->id)->orderBy('id')->get();
                        $inv_id="";
                        foreach($invoices_po as $inv){
                            $inv_id.=$inv->invoice_no."\n\r".",";

                           // $inv_id.=$inv->invoice_no;

                        }

                        return $inv_id;
                    })
                    ->editColumn('invoice_amount', function ($purchase) {
                        $invoices_po = DB::table('purchase_invoices')->select('*')->where('purchase_id',$purchase->id)->orderBy('id')->get();
                        $inv_amount="";

                        foreach($invoices_po as $inv){

                            $inv_amount.=round($inv->invoice_amount,2)."\n\r".",";

                        }

                        return $inv_amount;
                    })
					 ->editColumn('invoice_file', function ($purchase) {
                        $invoices_po = DB::table('purchase_invoices')->select('invoice_file')->where('purchase_id',$purchase->id)->orderBy('id')->get();
						 $file="";
                           foreach($invoices_po as $inv){
                       if($inv->invoice_file !=""){
					  $file.='<a href="'.e(\App\Support\SecureUploads::url('invoice', $inv->invoice_file)).'" class=\"btn btn-success btn-sm\">View</a>'."\n\r"." &nbsp&nbsp ";
					   }else{
						    $file.='<a href= "#" class=\"btn btn-success btn-sm\"></a>'."\n\r"." &nbsp&nbsp ";
					   }

                    }
                        return $file;

                    })
					 ->editColumn('co2', function ($purchase) {
                        $co = DB::table('purchase_invoices')->select('*')->where('purchase_id',$purchase->id)->orderBy('id')->get();
                        $inv_co="";

                        foreach($co as $inv){

                            $inv_co.=round($inv->co2,2)."\n\r".",";

                        }

                        return $inv_co;
                    })

                   ->addColumn('delivery', function ($purchase) {
                        $invoices_po = DB::table('purchase_deliverynote')->select('delivery_note')->where('purchase_no',$purchase->id)->orderBy('id')->get();
						 $delivery="";
                           foreach($invoices_po as $inv){


					  $delivery.='<a href="'.e(\App\Support\SecureUploads::url('delivery', $inv->delivery_note)).'" class=\"btn btn-success btn-sm\">View</a>'."\n\r"." &nbsp,&nbsp ";


                    }
                        return $delivery;

                    })
					->addColumn('cer', function ($purchase) {
						$cer="";
                        $invoices_po = DB::table('purchase_certificate')->select('certificate')->where('purchase_no',$purchase->id)->orderBy('id')->get();
                           foreach($invoices_po as $inv){


					  $cer.='<a href="'.e(\App\Support\SecureUploads::url('certificate', $inv->certificate)).'" class=\"btn btn-success btn-sm\">View</a>'."\n\r"." &nbsp,&nbsp ";


                    }
                       return $cer;

                    })
                    ->addColumn('photo', function ($purchase) {
						$photo="";
                        $invoices_po = DB::table('purchase_orders')->select('photo')->where('purchase_id',$purchase->id)->orderBy('id')->get();
                           foreach($invoices_po as $inv){


					  $photo.='<a href="'.e(\App\Support\SecureUploads::url('photo', $inv->photo)).'" class=\"btn btn-success btn-sm\">View</a>'."\n\r"." &nbsp,&nbsp ";


                    }
                       return $photo;

                    })
                    ->addColumn('action', function ($purchase) {
                        $actions = "";
                        $actions .= "<a title=\"View purchase order\" href=\"" . route('purchase.orders.view', $purchase->id) . "\" class=\"btn btn-primary btn-sm\"><i class=\"fas fa-eye\"></i></a>";
						//if (auth()->user()->can('access', 'purchase orders add') && (auth()->user()->isRole('Super Admin') || auth()->user()->isRole('Admin')))
                        if (auth()->user()->can('access', 'purchase orders add')) {
                            $actions .= "&nbsp;<a title=\"Print & View purchase order\" href=\"" . route('purchase.orders.print.view', $purchase->id) . "\" class=\"btn btn-warning btn-sm\"><i class=\"fas fa-print\"></i></a>";
                            $actions .= "&nbsp;<a title=\"Edit purchase order\" href=\"" . route('purchase.orders.edit', $purchase->id) . "\" class=\"btn btn-info btn-sm\"><i class=\"fas fa-pencil-alt\"></i></a>";
                            $actions .= "&nbsp;<a title=\"Delete purchase order\" onclick=\"return confirm('Are you sure want to remove the purchase order?')\" href=\"" . route('purchase.orders.delete', $purchase->id) . "\"  class=\"btn btn-danger btn-sm\"><i class=\"fas fa-trash\"></i></a>";
                        }
                        return $actions;
                    })
					->filter(function ($instance) use ($request) {
                        if((!auth()->user()->isRole('Admin')) && (!auth()->user()->isRole('Super Admin')) && (!auth()->user()->isRole('Supplier'))){
                        if (!empty($request->get('search'))) {
                             $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('supplier_name', 'LIKE', "%$search%")
								->orWhere('revision_no', 'LIKE', "%$search%")
								->orWhere('unique_reference_no', 'LIKE', "%$search%")
								->orWhere('grand_total', 'LIKE', "%$search%")
								->orWhere('v_purchase_delivery_certificate_supplier.created_at', 'LIKE', "%$search%")
								->orWhere('delivery_date', 'LIKE', "%$search%");
                            });
                        }
						}else{
						if (!empty($request->get('search'))) {
                             $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('supplier_name', 'LIKE', "%$search%")
								->orWhere('revision_no', 'LIKE', "%$search%")
								->orWhere('unique_reference_no', 'LIKE', "%$search%")
								->orWhere('grand_total', 'LIKE', "%$search%")
								->orWhere('created_at', 'LIKE', "%$search%")
								->orWhere('delivery_date', 'LIKE', "%$search%");
                            });
                        }
                        }
                    })
                    ->rawColumns(['revision_no', 'grand_total', 'action','delivery','cer','invoice_file','photo'])
                    ->make(true);
        }
    }

    public function getQuotationForm(Request $request){
        $view = "";
         $project = Project::find($request->query('project_id'));
        if($request->ajax()){
            $subActivity = SubActivity::find($request->query('sub_activity_id'));
            if($subActivity){
                $query = $subActivity->activities();
                $query->where(function($q){
                    $q->where('activity', 'NOT LIKE', '%labour%');
                    $q->where('activity', 'NOT LIKE', '%install%');
                });
                $activities = $query->get();
                $activities = $activities->filter(function($item, $key){
                                    if(($item->activity != 'Labour') || ($item->activity != 'labour') || ($item->activity != 'Install') || ($item->activity != 'install')){
                                        return true;
                                    }
                                });

                $view = view('purchasemanager::quotations.add_form', compact('activities','project'))->render();
            }
        }
        return response()->json(['html'=> $view]);
    }

    /**
     * Get purchase order from.
     * @param Request $request
     * @return Response
     */
    public function getPurchaseOrderForm(Request $request){
        $view = "";
        $project = Project::find($request->query('project_id'));

        if($request->ajax()){
            $subActivity = SubActivity::find($request->query('sub_activity_id'));
            if($subActivity){
                $query = $subActivity->activities();
                $query->where('activity', 'NOT LIKE', '%labour%');
                $activities = $query->get();
                $activities = $activities->filter(function($item, $key){
                                    if(($item->activity != 'Labour') || ($item->activity != 'labour')){
                                        return true;
                                    }
                                });

                $view = view('purchasemanager::purchase_orders.add_form', compact('activities','project'))->render();
            }
        }
        return response()->json(['html'=> $view]);
    }

    /**
     * Get separate purchase order from.
     * @param Request $request
     * @return Response
     */
    public function getSeparatePurchaseOrderForm(Request $request){
        $view = "";
        $project = Project::find($request->query('project_id'));

        if($request->ajax()){
            $subActivity = SubActivity::find($request->query('sub_activity_id'));
            if($subActivity){
                $query = $subActivity->activities();
                $query->where('activity', 'NOT LIKE', '%labour%');
                $activities = $query->get();
                $activities = $activities->filter(function($item, $key){
                                    if(($item->activity != 'Labour') || ($item->activity != 'labour')){
                                        return true;
                                    }
                                });


                $suppliers = \App\User::whereHas('roles', function($q){
                    $q->whereName('Supplier');
                })->whereStatus(1);

                if(!auth()->user()->isRole('Super Admin')){
                    if(auth()->user()->isRole('Admin')){
                        $suppliers->where('company_id', auth()->id());
                    }else{
                        $suppliers->where('company_id', auth()->user()->company_id);
                    }
                }
                $suppliers = $suppliers->pluck('supplier_name', 'id');
                $suppliers->prepend('Select', '');

                $view = view('purchasemanager::purchase_orders.add_form_separate', compact('activities', 'suppliers','project'))->render();
            }
        }
        return response()->json(['html'=> $view]);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function getAreasAndLevels(Request $request){
        $areas = [];
        $levels = [];
        if($request->ajax()){
            $projectId = $request->query('project_id');
            $project = Project::find($projectId);
            if($project){
                $areas = $project->mainActivities->pluck('area_display_name', 'id');
                $areas->prepend('Select area', '');

                $levels = $project->mainActivities->pluck('level_display_name', 'id');
                $levels->prepend('Select level', '');
            }
        }
        return response()->json([
            'areas'=> $areas,
            'levels' => $levels
        ]);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function getAreas(Request $request){
        $areas = [];
        if($request->ajax()){
            $projectId = $request->query('project_id');
            $project = Project::find($projectId);
            if($project){
                $areas = $project->mainActivities->pluck('area_display_name', 'id');
            }
        }
        return response()->json($areas);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function getLevels(Request $request){
        $levels = [];
        if($request->ajax()){
            $projectId = $request->query('project_id');
            $project = Project::find($projectId);
            if($project){
                $mainActivityId = $request->query('main_activity_id');
                $levels = $project->mainActivities->where('id', $mainActivityId)->pluck('level_display_name', 'id');
            }
        }
        return response()->json($levels);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function getSubCodes(Request $request){
        $subCodes = [];
        if($request->ajax()){
            $mainActivityId = $request->query('main_activity_id');
            $mainActivity = MainActivity::find($mainActivityId);
            if($mainActivity){
                $subActivities = $mainActivity->subActivities;
                $subCodes = $subActivities->pluck('activity_display_name', 'id');
            }
        }
        return response()->json($subCodes);
    }


}
