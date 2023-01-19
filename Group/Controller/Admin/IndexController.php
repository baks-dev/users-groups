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

namespace App\Module\Users\Groups\Group\Controller\Admin;


use App\Module\Users\Groups\Group\Repository\AllGroups\AllGroupsInterface;
use App\System\Controller\AbstractController;
use App\System\Form\Search\SearchDTO;
use App\System\Form\Search\SearchForm;
use App\System\Services\Paginator;
use App\System\Services\Paginator\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_GROUPS" in role_names'))]
final class IndexController extends AbstractController
{
	#[Route('/admin/groups/{page<\d+>}', name: 'admin.index', methods: ['GET', 'POST'])]
	public function index(
		Request $request,
		AllGroupsInterface $allGroups,
		int $page = 0,
	) : Response
	{
		/* Поиск */
		$search = new SearchDTO();
		$searchForm = $this->createForm(SearchForm::class, $search);
		$searchForm->handleRequest($request);
		
		/* Получаем список */
		$query = $allGroups->get($search);
		
		return $this->render([
			'query' => $query,
			'search' => $searchForm->createView(),
		]);
	}
}