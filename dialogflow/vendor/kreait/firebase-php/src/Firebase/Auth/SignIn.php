<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

interface SignIn
{
    public function withTenantId(string $tenantId): self;

    public function tenantId(): ?string;
}
