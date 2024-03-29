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

namespace BaksDev\Users\Groups\Users\Controller\Admin\Tests;

use BaksDev\Users\User\Tests\TestUserAccount;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/** @group users-groups */
#[When(env: 'test')]
final class IndexControllerTest extends WebTestCase
{
    private const URL = '/admin/user/checks';
    private const ROLE = 'ROLE_CHECK_USERS';

    /** Доступ по роли  */
    public function testRoleSuccessful(): void
    {
        $client = static::createClient();

        $user = TestUserAccount::getModer(self::ROLE);

        $client->loginUser($user, 'user');
        $client->request('GET', self::URL);

        self::assertResponseIsSuccessful();

    }

    /** Доступ по роли ROLE_ADMIN */
    public function testRoleAdminSuccessful(): void
    {
        $client = static::createClient();

        $user = TestUserAccount::getAdmin();

        $client->loginUser($user, 'user');
        $client->request('GET', self::URL);

        self::assertResponseIsSuccessful();
    }

    /** Доступ закрыт по роли ROLE_USER */
    public function testRoleUserDany(): void
    {
        $client = static::createClient();

        $user = TestUserAccount::getUser();
        $client->loginUser($user, 'user');
        $client->request('GET', self::URL);

        self::assertResponseStatusCodeSame(403);
    }

    /** Доступ закрыт без роли */
    public function testGuestDany(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', self::URL);

        // Full authentication is required to access this resource
        self::assertResponseStatusCodeSame(401);
    }
}
