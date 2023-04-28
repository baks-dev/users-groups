<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Users\Groups\Group\DataFixtures\Security;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Users\Groups\Group\DataFixtures\Security\Group\GroupFixtures;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRoleHandler;
use BaksDev\Users\Groups\Role\Entity\Role;
use BaksDev\Users\Groups\Role\UseCase\Admin\NewEdit\RoleHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RoleFixtures extends Fixture implements DependentFixtureInterface
{
    private RoleHandler $roleAggregate;

    private CheckRoleHandler $checkRoleAggregate;
    private iterable $roles;
    private iterable $voters;
    private TranslatorInterface $translator;

    public function __construct(
        iterable $roles,
        iterable $voters,
        RoleHandler $roleAggregate,
        CheckRoleHandler $checkRoleAggregate,
        TranslatorInterface $translator
    ) {
        $this->roles = $roles;
        $this->voters = $voters;

        $this->roleAggregate = $roleAggregate;
        $this->checkRoleAggregate = $checkRoleAggregate;
        $this->translator = $translator;
    }

    /**
     * Метод добавляет роли и правила через итерацию интерфейса.
     *
     * @see RoleFixturesInterface
     * @see VoterFixturesInterface
     */
    public function load(ObjectManager $manager): void
    {
        // php bin/console doctrine:fixtures:load --append

        /** @var RoleFixturesInterface $role */
        foreach ($this->roles as $role) {
            // Проверяем имеется ли такая роль
            $RoleEvent = $manager->getRepository(Role::class)->find($role->getRole());

            /** Создаем роль */
            $RoleDTO = new RoleFixtures\RoleDTO($role);
            $RoleDTO->setId($RoleEvent ? $RoleEvent->getEvent() : null);

            // Настройки локали роли
            foreach (Locale::cases() as $local) {
                $RoleTransDTO = new RoleFixtures\RoleTransDTO($local);

                // Название роли
                $RoleName = $this->translator->trans(id: $RoleDTO->getRole().'.name', domain: 'security', locale: $local);
                $RoleTransDTO->setName($RoleName);

                if ($RoleName === $RoleDTO->getRole().'.name') {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Для префикса роли %s не добавлено название в файл переводов домена security локали %s: %s',
                            $RoleDTO->getRole(),
                            $local,
                            $role::class
                        )
                    );
                }

                // Краткое описание роли
                $RoleDesc = $this->translator->trans(id: $RoleDTO->getRole().'.desc', domain: 'security', locale: $local);
                $RoleTransDTO->setDescription($RoleDesc);

                if ($RoleDesc === $RoleDTO->getRole().'.desc') {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Для префикса роли %s не добавлено краткое описание в файл переводов домена security локали %s: %s',
                            $RoleDTO->getRole(),
                            $local,
                            $role::class
                        )
                    );
                }

                $RoleDTO->addTranslate($RoleTransDTO);
            }

            /** @var VoterFixturesInterface $voter */
            foreach ($this->voters as $voter) {
                if ($voter->equals($role)) {
                    $VoterDTO = new RoleFixtures\Voter\VoterDTO($voter);

                    // Настройки локали равила
                    foreach (Locale::cases() as $local) {
                        $VoterTransDTO = new RoleFixtures\Voter\VoterTransDTO($local);

                        // Название правила
                        $VoterName = $this->translator->trans(id: $VoterDTO->getVoter().'.name', domain: 'security', locale: $local);

                        if ($VoterName === $VoterDTO->getVoter().'.name') {
                            throw new InvalidArgumentException(
                                sprintf(
                                    'Для префикса правила %s не добавлено название в файл переводов домена security локали %s: %s',
                                    $VoterDTO->getVoter(),
                                    $local,
                                    $voter::class
                                )
                            );
                        }

                        $VoterTransDTO->setName($VoterName);

                        $VoterDTO->addTranslate($VoterTransDTO);
                    }

                    $RoleDTO->addVoter($VoterDTO);
                }
            }

            /** Сохраняем роль */
            $RoleHandle = $this->roleAggregate->handle($RoleDTO);

            if (!$RoleHandle instanceof Role) {
                throw new InvalidArgumentException(
                    sprintf('Ошибка %s при обнововнении роли', $RoleHandle)
                );
            }

            // Применяем правила и роли к администратору

            /** @var GroupEvent $GroupEvent */
            $GroupEvent = $this->getReference(GroupFixtures::class);

            $CheckRoleDTO = new RoleFixtures\Check\CheckRoleDTO($GroupEvent, $RoleDTO);
            $this->checkRoleAggregate->handle($CheckRoleDTO);
        }
    }

    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
        ];
    }
}
