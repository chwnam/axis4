<?php


namespace Naran\Axis\Interfaces;


interface Registrable
{
    const AJAX         = 'ajax';
    const BLOCK        = 'block';
    const CACHE        = 'cache';
    const COMMENT_META = 'comment_meta';
    const CRON         = 'cron';
    const OPTION       = 'option';
    const POST_META    = 'post_meta';
    const SCRIPT       = 'script';
    const SCRIPT_ADMIN = 'script/admin';
    const SCRIPT_FRONT = 'script/front';
    const SHORTCODE    = 'shortcode';
    const STYLE        = 'style';
    const STYLE_ADMIN  = 'style/admin';
    const STYLE_FRONT  = 'style/front';
    const TERM_META    = 'term_meta';
    const USER_META    = 'user_meta';

    public function register();

    public function unregister();
}