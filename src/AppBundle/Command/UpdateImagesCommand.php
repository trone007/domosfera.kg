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

class UpdateImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:images-update')
            ->setDescription('Updates image located in tmp folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        $wallpapers = $doctrine->getRepository('AppBundle:Wallpaper')->findByShop('kgb');

        $dir = '/home/denis/project/web/tmp/';

        foreach ($wallpapers as $wallpaper) {
            $filename = $dir. $wallpaper->getImage() . '300x300' . '.jpg';
            if (file_exists($filename)) {
                continue;
            }
            $image = file_get_contents('http://www.gallery.kg/image/'. urlencode('кыргызстан') .
                '/' . $wallpaper->getImage(). '/300/300');

            $image = imagecreatefromstring($image);
            imagejpeg($image, $filename, 50);
            imagedestroy($image);
        }



        $output->write('WellDone');
    }

}