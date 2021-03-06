<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\DataFixtures;

use KejawenLab\ApiSkeleton\Entity\Setting;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class SettingFixture extends AbstractFixture
{
    protected function createNew()
    {
        return new Setting();
    }

    protected function getReferenceKey(): string
    {
        return 'setting';
    }
}
