<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace App\Module\Users\Groups\Users\Controller\Admin;

use App\Module\Users\Groups\Users\UseCase\Admin\Add\CheckUsersDTO;
use App\Module\Users\Groups\Users\UseCase\Admin\Add\CheckUsersForm;
use App\Module\Users\Groups\Users\UseCase\CheckUserAggregate;
use App\System\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_CHECK_USERS_NEW" in role_names'))]
final class NewController extends AbstractController
{
    #[Route('/admin/user/check/new', name: 'admin.new', methods: ['GET', 'POST'])]
    public function new(
      Request $request,
      CheckUserAggregate $aggregate
    ) : Response
    {
        $CheckUsersDTO = new CheckUsersDTO();
        
        /* Форма добавления */
        $form = $this->createForm(CheckUsersForm::class, $CheckUsersDTO, [
          'action' => $this->generateUrl('GroupCheckUser:admin.new'),
        ]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $handle = $aggregate->handle($CheckUsersDTO);
            
            if($handle)
            {
                $this->addFlash('success', 'admin.new.success', 'groups.users');
                return $this->redirectToRoute('GroupCheckUser:admin.index');
            }
    
            $this->addFlash('danger', 'admin.new.danger', 'groups.users');
            return $this->redirectToRoute('GroupCheckUser:admin.index');
        }
        
        return $this->render(['form' => $form->createView()]);
        
    }

}