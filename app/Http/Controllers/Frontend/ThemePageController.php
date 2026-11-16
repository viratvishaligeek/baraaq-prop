<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ThemePageController extends Controller
{
    private const PAGES=['about','agency','agency-details','blog-details','blog-grid-left-sidebar','blog-grid-right-sidebar','contact','error','faq','gallery','home-10','home-2','home-3','home-4','home-5','home-6','home-7','home-8','home-9','neighborhood-guide','pricing','service','service-details','service-right-sidebar','team','team-details'];

    public function show(string $template)
    {
        abort_unless(in_array($template,self::PAGES,true),404);
        if($template==='contact') return $this->render($template,true);
        return $this->render($template,false);
    }

    private function render(string $template,bool $contact)
    {
        $path=base_path('theme-files-html/'.$template.'.html');
        abort_unless(File::exists($path),404);
        $html=File::get($path);
        $html=preg_replace_callback('/((?:src|href|data-bg-src)=["\'])assets\/([^"\']+)/',fn($m)=>$m[1].asset('theme-assets/'.$m[2]),$html);
        $html=str_replace('href="index.html','href="/"',$html);
        if($contact){
            $form='<form action="'.route('enquiries.store').'" method="POST" class="contact-form ajax-contact">'.csrf_field();
            $html=str_replace('<form action="https://html.themehour.net/piller/demo/mail.php" method="POST" class="contact-form ajax-contact">',$form,$html);
            $html=str_replace('name="number"','name="phone"',$html);
            $html=str_replace('name="Your Messsage*"','name="message"',$html);$html=str_replace('class="contact-form ajax-contact"','class="contact-form"',$html);
        }
        if($contact){$feedback='';if(session('enquiry_success'))$feedback.='<div class="alert alert-success">'.e(session('enquiry_success')).'</div>';$validationErrors=session('errors');if($validationErrors&&$validationErrors->any())$feedback.='<div class="alert alert-danger">'.e($validationErrors->first()).'</div>';$html=str_replace('<form action="'.route('enquiries.store').'" method="POST" class="contact-form">',$feedback.'<form action="'.route('enquiries.store').'" method="POST" class="contact-form">',$html);}
        return view('frontend.theme-page',['html'=>$html]);
    }
}