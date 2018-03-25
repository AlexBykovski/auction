<?php

namespace App\Command;

ini_set('max_execution_time', 60);

use App\Entity\Product;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckFinishProductCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('app:product:check-finish')
            ->setDescription('Check finish auctions');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $time = new DateTime();
        $time->add(new DateInterval("PT1M"));

        $container = $this->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.default_entity_manager');

        while($time > (new DateTime())){
            $products = $em->getRepository(Product::class)->findAllAlreadyFinished();

            //$output->writeln(count($products));

            /** @var Product $product */
            foreach($products as $product){
                $product->setEndAt(new DateTime());
                $product->setWinner($product->getPotentialWinner());
            }

            $em->flush();
        }


        //$output->writeln("<info>Done!</info>");
    }
}