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
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Users\Groups\Users\Entity\Event\CheckUsersEvent;
use BaksDev\Users\Groups\Users\UseCase\Admin\Delete\DeleteCheckUserDTO;
use BaksDev\Users\Groups\Users\UseCase\Admin\Delete\DeleteCheckUserForm;
use BaksDev\Users\Groups\Users\UseCase\CheckUserAggregate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_CHECK_USERS_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/user/check/delete/{id}', name: 'admin.delete', methods: ['POST', 'GET'])]
    public function delete(
        Request $request,
        CheckUserAggregate $aggregate,
        UserAccountEventInterface $account,
        CheckUsersEvent $Event,
    ): Response {
        $DeleteCheckUserDTO = new DeleteCheckUserDTO();
        $Event->getDto($DeleteCheckUserDTO);

        $form = $this->createForm(DeleteCheckUserForm::class, $DeleteCheckUserDTO, [
            'action' => $this->generateUrl('GroupCheckUser:admin.delete', ['id' => $DeleteCheckUserDTO->getEvent()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->has('delete')) {
            $handle = $aggregate->handle($DeleteCheckUserDTO);

            if ($handle) {
                $this->addFlash('success', 'admin.delete.success', 'groups.users');

                return $this->redirectToRoute('GroupCheckUser:admin.index');
            }

            $this->addFlash('danger', 'admin.delete.danger', 'groups.users');

            return $this->redirectToRoute('GroupCheckUser:admin.index');
            // return $this->redirectToReferer();
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
