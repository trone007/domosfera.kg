<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 02/05/17
 * Time: 17:55
 */

namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Image as Image;

class GetImageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:image-get')
            ->setDescription('Updates image located in tmp folder')
            ->addArgument('image', InputArgument::REQUIRED, 'UUID of the image located in ONE C service')
            ->addArgument('width', InputArgument::REQUIRED, 'width of the image')
            ->addArgument('height', InputArgument::REQUIRED, 'height of the image');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('image');
        $width = $input->getArgument('width');
        $height = $input->getArgument('height');

        $rootDir = dirname($this->getContainer()->get('kernel')->getRootDir());
        $dir = $rootDir . '/web/tmp/';

        $filename = $dir. $id .$width . 'x' . $height.'.jpg';

        if(file_exists($filename)) {
            $output->write(base64_encode(file_get_contents($filename)));
        } else {
            $output->write(
                base64_encode(
                    file_get_contents('http://www.gallery.kg/image/'. urlencode('кыргызстан'). '/' . $id . '/'.$width. '/'. $height)
                )
            );
        }
    }

}