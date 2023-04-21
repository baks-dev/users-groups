<?php
/*
 * Copyright (c) 2023.  Baks.dev <admin@baks.dev>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BaksDev\Users\Groups\Users\Controller\Admin;

use BaksDev\Auth\Email\Repository\UserAccountEvent\UserAccountEventInterface;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEvent;
use BaksDev\Users\Groups\Users\UseCase\Admin\Edit\CheckUsersDTO;
use BaksDev\Users\Groups\Users\UseCase\Admin\Edit\CheckUsersForm;
use BaksDev\Users\Groups\Users\UseCase\CheckUserAggregate;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_CHECK_USERS_EDIT" in role_names'))]
final class EditController extends AbstractController
{
    #[Route('/admin/user/check/edit/{id}', name: 'admin.edit', methods: ['GET', 'POST'])]
    public function check(
        Request $request,
        CheckUserAggregate $aggregate,
        UserAccountEventInterface $account,
        #[MapEntity] CheckUsersEvent $Event,
    ): Response {

        $CheckUsersDTO = new CheckUsersDTO();
        $Event->getDto($CheckUsersDTO);

        // Форма добавления
        $form = $this->createForm(CheckUsersForm::class, $CheckUsersDTO, [
            'action' => $this->generateUrl('GroupCheckUser:admin.edit', ['id' => $Event->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $handle = $aggregate->handle($CheckUsersDTO);

            if ($handle) {
                $this->addFlash('success', 'admin.update.success', 'groups.users');

                return $this->redirectToRoute('GroupCheckUser:admin.index');
            }

            $this->addFlash('danger', 'admin.update.danger', 'groups.users');

            return $this->redirectToRoute('GroupCheckUser:admin.index');
        }

        $userAccount = $account->getAccountEventByUser($Event->getUser());

        return $this->render(
            [
                'form' => $form->createView(),
                'name' => $userAccount?->getEmail(),
            ]
        );
    }
}
