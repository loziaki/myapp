<?php
namespace View\Admin;

use Model\User;
use Model\Team;
use Model\Progress as ProjectProgress;
use Model\Lesson;

class AdminBaseView extends \Framework\BaseView
{
    const SUCESS_CODE = 20000;

    const TOKEN_NAME = 'x-token';

    const ERR_NOTICE_USER = 50333;
    const ERR_ILLEGAL_TOKEN = 50008;
    const ERR_OTHER_CLIENTS_LOGGED_IN = 50012;
    const ERR_TOKEN_EXPIRED = 50014;

    const ALLOW_ORIGIN_NAME = 'http://localhost:9528';

    const ALLOW_HEADERS_NAMES = [self::TOKEN_NAME,'content-type'];

    const PAGE_ITEMS_COUNT = 20;
    /**
     * $request \Symfony\Component\HttpFoundation\Request
     */
    protected function getPaginationParam($request)
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', static::PAGE_ITEMS_COUNT);
        $sort = $request->query->get('sort');

        $offset = ($page - 1) * $limit;

        return [
            'page' => $page,
            'limit' => $limit,
            'sort' => $sort,
            'offset' => $offset
        ];
    }
}
