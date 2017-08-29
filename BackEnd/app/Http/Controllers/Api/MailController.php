<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    protected $apiUser;

    protected $apiKey;

    protected $apiUrl;

    protected $from;

    protected $fromName;

    const NEW_TRANSLATE_TASK = 1;

    const NEW_REVIEW_TASK = 2;

    const NEW_ARTICLE = 3;

    public function __construct()
    {
        $this->apiUser  = env('MAIL_USER');
        $this->apiKey   = env('MAIL_KEY');
        $this->apiUrl   = env('MAIL_HOST');
        $this->from     = env('MAIL_FROM');
        $this->fromName = env('MAIL_FROMNAME');
    }

    public function sendMail($to, $subject, $html)
    {
        $params = array(
                'apiUser'   => $this->apiUser,
                'apiKey'    => $this->apiKey,
                'from'      => $this->from,
                'to'        => $to,
                'subject'   => $subject,
                'html'      => $html
            );
        return $this->sendRequest($this->apiUrl, 'POST', $params);
    }

    public function activate($id)
    {
        $applicant = DB::table('applicant')
                        ->select('email', 'invitation', 'comment as reason')
                        ->where('id', $id)
                        ->first();

        $subject = $applicant->invitation ? "欢迎加入掘金翻译计划！" : "很遗憾，您没有通过我们的审核！";
        echo $this->sendMail($applicant->email, $subject, view("mails/active", ['invitationCode' => $applicant->invitation])->render());
    }

    public function notify()
    {
        $articles = DB::table('translation')
                    ->select('id', 'poster', 'description', 'title')
                    ->where('status', '0')
                    ->get();
        return view("mails/notifications", ["articles" => $articles]);
    }

    public function result($id)
    {
        $article = DB::table('recommend')
                    ->join('user', 'recommend.recommender', '=', 'user.id')
                    ->select('recommend.title', 'recommend.status as result', 'user.email', 'recommend.comment as reason')
                    ->where('recommend.id', $id)
                    ->first();
        $subject = ($article->result == 1) ? "您推荐文章已经通过啦！" : "很遗憾，您推荐的文章未通过审核。";
        echo $this->sendMail($article->email, $subject, view("mails/result", ['article' => $article])->render());
    }
}
