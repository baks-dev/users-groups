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

namespace BaksDev\Users\Groups\Role\Type\VoterPrefix;

use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class VoterPrefixType extends StringType
{
    public const NAME = 'voter_prefix';
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) : mixed
    {
        return $value instanceof VoterPrefix ? $value->getValue() : $value;
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) : mixed
    {
        return !empty($value) ? new VoterPrefix($value) : null;
    }
    
    public function getName() : string
    {
        return self::NAME;
    }
    
    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
    
}