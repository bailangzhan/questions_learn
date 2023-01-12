<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Request\GetCodeForSignUpRequest;
use App\Service\MailService;
use Hyperf\Di\Annotation\Inject;

class MailController extends AbstractController
{
    #[Inject]
    protected MailService $mailService;

    public function getCode(GetCodeForSignUpRequest $getCodeForSignUpRequest)
    {
        $email = $getCodeForSignUpRequest->input('email');

        $this->mailService->getCode($email);

        return $this->response->success();
    }
}
