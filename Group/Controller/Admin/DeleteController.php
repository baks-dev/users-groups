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

namespace BaksDev\Users\Groups\Group\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\Entity\Group;
use BaksDev\Users\Groups\Group\UseCase\Admin\Delete\DeleteGroupDTO;
use BaksDev\Users\Groups\Group\UseCase\Admin\Delete\DeleteGroupForm;
use BaksDev\Users\Groups\Group\UseCase\Admin\Delete\DeleteGroupHandler;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[RoleSecurity('ROLE_GROUPS_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route(
        '/admin/group/delete/{id}',
        name: 'admin.delete',
        methods: ['POST', 'GET']
    )]
    public function delete(
        Request $request,
        DeleteGroupHandler $handler,
        GroupEvent $Event,
    ): Response {
        $GroupDTO = new DeleteGroupDTO();
        $Event->getDto($GroupDTO);

        $form = $this->createForm(DeleteGroupForm::class, $GroupDTO, [
            'action' => $this->generateUrl('UserGroup:admin.delete', ['id' => $GroupDTO->getEvent()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->has('delete')) {
            $GroupEvent = $handler->handle($GroupDTO);

            if ($GroupEvent instanceof Group) {
                $this->addFlash('success', 'admin.success.delete', 'groups.group');

                return $this->redirectToRoute('UserGroup:admin.index');
            }

            $this->addFlash('danger', 'admin.danger.delete', 'groups.group', $GroupEvent);

            return $this->redirectToRoute('UserGroup:admin.index', status: 400);
        }

        return $this->render(
            [
                'form' => $form->createView(),
                'name' => $Event->getNameByLocale($this->getLocale()), // название согласно локали
            ]
        );
    }
}
