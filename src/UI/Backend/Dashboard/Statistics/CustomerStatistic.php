<?php

declare(strict_types=1);

namespace App\UI\Backend\Dashboard\Statistics;

use App\Shared\Infrastructure\Persistence\Doctrine\ORM\CustomerRepository;
use Monofony\Component\Admin\Dashboard\Statistics\StatisticInterface;
use Twig\Environment;

class CustomerStatistic implements StatisticInterface
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly Environment $twig,
    ) {
    }

    public function generate(): string
    {
        $amountCustomers = $this->customerRepository->countCustomers();

        return $this->twig->render('backend/dashboard/statistics/_amount_of_customers.html.twig', [
            'amountOfCustomers' => $amountCustomers,
        ]);
    }

    public static function getDefaultPriority(): int
    {
        return -1;
    }
}
