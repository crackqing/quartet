<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
/**
 * 存放未定义 class
 */
class OtherController extends CommonController
{
    /**
     * FAQ TEMLPLTE function
     *
     * @return void
     */
    public function quesstion()
    {
        return view('Front.other.faq');
    }
}
