<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller; use App\Models\Enquiry; use Illuminate\Http\Request;
class EnquiryController extends Controller { public function store(Request $r){$d=$r->validate(['property_id'=>['nullable','exists:properties,id'],'name'=>['required','string','max:150'],'email'=>['nullable','email','max:190'],'phone'=>['required','string','max:30'],'message'=>['nullable','string','max:5000']]);Enquiry::create($d+['source'=>'website','status'=>'new']);return back()->with('enquiry_success','Thank you. Your enquiry has been received.');} }