UPGRADE FROM 2.x to 3.0
=======================

Requirements
------------

 * PHP 8.4 or higher (was 8.1+)
 * Symfony 6.4 or higher (was 5.4+)
 * `doctrine/doctrine-bundle` 2.18+ or 3.0+ (was 2.11+)

Symfony HttpKernel
------------------

 * [BC BREAK] The `BackedEnumValueResolver` class has been removed.
   Symfony natively resolves backed enum cases from route path parameters since version 6.1.
   No action is required if you were not referencing this class directly.

 * [BC BREAK] The `#[BackedEnumFromQuery]` attribute has been removed.
   Use Symfony's native `#[MapQueryParameter]` attribute instead, available since Symfony 6.3:

   ```diff
   -use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromQuery;
   +use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

    class DefaultController
    {
        #[Route('/cards')]
        public function list(
   -        #[BackedEnumFromQuery]
   +        #[MapQueryParameter]
            ?Suit $suit = null,
        ): Response {
            // ...
        }
    }
   ```

 * The `#[BackedEnumFromBody]` attribute is still available, as Symfony does not provide a native equivalent
   for resolving individual backed enum parameters from the request body.
