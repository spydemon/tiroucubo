<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Repository\PathRepository;
use Doctrine\Bundle\FixturesBundle\Tests\Fixtures\FooBundle\DataFixtures\WithDependenciesFixtures;
use Doctrine\Persistence\ObjectManager;
use \DateTime;

class ArticleFixtures extends WithDependenciesFixtures
{
    private string $dumbSummary = <<<HTML
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed a maximus sem. Duis commodo neque at arcu egestas luctus. Pellentesque vehicula, dui quis mattis placerat, ante sapien posuere libero, eu venenatis turpis quam quis augue. Nullam rutrum nulla libero, a tristique orci tempus ut. Nunc non dolor imperdiet, elementum nibh quis, posuere odio. Vestibulum et tortor dictum, congue augue vitae, scelerisque leo. Etiam suscipit purus pharetra augue aliquam rhoncus. Sed iaculis tempus facilisis. Donec bibendum dolor eu lacus scelerisque placerat. Aliquam a tincidunt enim. Vivamus maximus nunc metus, eget porttitor augue cursus id.</p>
   HTML;

    private string $dumbContent = <<<HTML
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam condimentum massa non ligula imperdiet, nec condimentum massa pretium. Aenean eget faucibus turpis. Vestibulum convallis venenatis velit, at euismod urna maximus sed. Etiam aliquet odio id faucibus blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus eu purus eget lectus vestibulum gravida. Donec nec diam a mi pharetra suscipit. Proin ut blandit risus, et vestibulum ante. Quisque magna mauris, faucibus et faucibus et, mollis vel eros. Morbi hendrerit nisi nec ex maximus aliquet. Maecenas pharetra nibh ut vehicula malesuada. Duis at vestibulum mi. Nam eget porta dolor. Proin vel rhoncus eros, nec ultrices risus.</p>
        <p>Ut id arcu libero. Aliquam ornare augue eget sapien dignissim, sed iaculis tortor bibendum. Etiam orci libero, pharetra non rutrum ut, tincidunt eu purus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed ac justo molestie metus condimentum convallis. Maecenas viverra magna non quam tempor dignissim. Aliquam vehicula nec sapien at dignissim. Nulla quis neque dictum, ultricies nibh id, lobortis erat. Aenean tincidunt condimentum arcu, id placerat nibh dictum et. Duis euismod eget mauris in elementum. Quisque eu massa quis quam ullamcorper dapibus. Vivamus vehicula enim feugiat enim sagittis, non imperdiet augue sodales. Etiam sit amet dignissim lectus, et mattis ligula.</p>
        <p>Duis tincidunt nisl eu eros dictum, condimentum sodales turpis ornare. Curabitur vestibulum viverra augue eu aliquam. Pellentesque vel ullamcorper nibh, at facilisis urna. Donec sed eleifend lorem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec feugiat nec ante id porttitor. Aliquam sit amet ultrices ex. Vivamus semper, velit nec aliquam gravida, mauris leo ultrices metus, in rutrum lectus mauris ultricies mauris. Curabitur est metus, varius id ex id, mollis varius elit. Nulla eu lobortis diam, vitae varius mauris. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Duis ex risus, finibus a turpis ac, imperdiet mollis urna. Phasellus fermentum feugiat tincidunt. Mauris consectetur lectus at enim imperdiet faucibus at eget justo.</p>
        <p>Donec vulputate quam eros, in euismod libero ornare tristique. Sed nec feugiat felis. Proin dui augue, hendrerit at odio in, maximus blandit metus. Vestibulum volutpat lorem fermentum eros eleifend, tempor suscipit nunc dapibus. Vivamus purus purus, mattis eleifend interdum id, lobortis ut ante. Nunc vulputate ultrices leo, a pellentesque magna varius bibendum. Suspendisse potenti.</p>
        <p>Integer porta ut quam elementum ornare. Interdum et malesuada fames ac ante ipsum primis in faucibus. Ut at auctor purus. Nullam a ultrices arcu. Sed sed ligula massa. Vestibulum orci arcu, iaculis rutrum sagittis et, convallis ac risus. Nunc at risus sed lorem blandit vehicula at ac diam. Vestibulum accumsan ultricies justo sit amet vestibulum. Aliquam justo tortor, eleifend ut maximus eget, sagittis a eros. Suspendisse vitae viverra odio. Nulla a sollicitudin enim. Sed malesuada molestie tellus a lobortis.</p>
        <p>Duis egestas consequat ante eu interdum. Sed vitae enim ipsum. Nullam blandit odio quis ultrices faucibus. Sed nec justo id lectus tempus dapibus id eget nulla. Integer non est justo. Sed ac hendrerit dui. Proin vehicula lobortis feugiat. Ut nec maximus lorem. Ut pulvinar consectetur sodales. Donec vulputate magna eu aliquet vulputate. Duis massa quam, pulvinar convallis lectus id, venenatis congue neque. Proin quis massa orci. Sed finibus nisi tortor, a aliquet augue tristique quis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;</p>
        <p>Nunc vitae mi non magna tempus sodales. Maecenas nec tellus gravida, interdum justo vel, fringilla ex. Donec eu ultricies turpis. In non vestibulum dui, ut scelerisque ex. Nam ac dignissim dui, eget ornare velit. Suspendisse potenti. Donec condimentum nibh et eleifend blandit. Sed vehicula ultrices urna, ac malesuada est ullamcorper eu. Aliquam erat volutpat. Phasellus porttitor rutrum nulla. Ut placerat orci odio, pulvinar fermentum erat pulvinar tincidunt. Vivamus laoreet, lorem sed ornare iaculis, purus mi aliquam risus, sed lacinia ex dolor sit amet tellus. Nulla sodales est sapien, vitae tincidunt mauris fermentum id. Suspendisse quis urna nec sapien lobortis faucibus. Donec non pulvinar nisl.</p>
        <p>Suspendisse sit amet erat erat. Vestibulum ultrices magna ut dui ultrices, in malesuada lacus maximus. Curabitur ac nunc eu nunc accumsan tempus eu ac sem. Aenean congue vehicula leo eu pretium. Sed tellus odio, convallis id orci nec, vehicula placerat neque. Sed nibh libero, tincidunt vitae viverra at, fringilla vitae ex. Quisque posuere, diam sit amet tempor tempus, nisi elit malesuada nibh, ac ornare ex libero eu ligula. Aliquam dapibus felis non justo porttitor, id semper magna ultrices. Proin sit amet elementum quam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Quisque eu tincidunt elit. In nec odio blandit, cursus quam mollis, condimentum risus. Sed ut dolor non urna finibus vehicula varius eget leo. Duis quis dui ut risus scelerisque vehicula. Integer in sapien egestas, commodo metus non, vestibulum neque.</p>
        <p>Vivamus turpis felis, dapibus eget tellus non, bibendum pharetra orci. Integer vel sodales neque. Phasellus accumsan posuere ex eget pharetra. Phasellus ac mauris lacinia, maximus est varius, laoreet odio. Suspendisse egestas finibus augue, a consectetur neque tempor sit amet. Suspendisse iaculis nibh erat, ut laoreet libero molestie ac. Donec venenatis pharetra ligula vel viverra. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
        <p>Ut quis arcu lacinia, dictum nisi vel, ultricies nibh. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In scelerisque nulla id tincidunt fermentum. Phasellus quis mauris at leo vehicula ullamcorper. Vivamus sit amet mollis nisi. Donec ullamcorper sodales est, ut auctor magna malesuada at. Donec varius dolor id fermentum semper. Mauris a neque ut magna condimentum pellentesque. Integer sit amet aliquam velit. Curabitur rhoncus lacus ut magna iaculis, vel sollicitudin risus ullamcorper. Donec eget sem dignissim, luctus ligula id, consequat ipsum. Phasellus ac luctus tellus, quis viverra dui.</p>
        <p>Aenean at ligula a enim gravida sollicitudin id ut nunc. Proin nec placerat nisl. Sed hendrerit a erat et faucibus. Vivamus consectetur dui in mauris faucibus consectetur. Sed placerat nibh eget nisl ultricies condimentum. Pellentesque lacinia convallis tellus non dignissim. Vestibulum nec ex sed dui commodo vulputate.</p>
        <p>Ut pharetra quis tellus a ultrices. Maecenas vitae neque eget sapien venenatis vulputate ut a ipsum. In hac habitasse platea dictumst. Fusce rutrum maximus consequat. Vivamus tellus felis, egestas ut sagittis semper, sollicitudin nec orci. Curabitur ipsum massa, mattis in turpis vel, rutrum faucibus libero. Sed feugiat lectus id augue suscipit, quis interdum velit eleifend. Nullam vel dui pulvinar, luctus odio eu, luctus velit. Aenean auctor a massa non sollicitudin. Nunc neque sapien, consequat sed vestibulum non, tempor eu odio.</p>
        <p>Sed eu eleifend diam. Donec dui orci, sodales non magna non, ultricies tincidunt quam. Praesent accumsan ante vitae velit gravida varius. Vivamus nunc dolor, semper imperdiet tellus nec, suscipit placerat nisl. Quisque ultrices et nibh vel dignissim. Donec enim metus, condimentum id justo eu, maximus cursus ligula. Quisque ut sapien scelerisque, commodo metus vitae, vestibulum eros. Vestibulum vel viverra quam, non dignissim mauris. Sed id eros bibendum, lobortis ante et, ultricies turpis. Praesent molestie volutpat leo ac tempus. Proin odio dolor, ornare sit amet lobortis sit amet, volutpat ut nisl.</p>
        <p>Nullam ligula libero, imperdiet ut sapien sed, elementum faucibus sem. Vestibulum aliquam sit amet est sed iaculis. Aliquam erat volutpat. Vivamus eget dolor sem. Curabitur commodo at sapien id interdum. In scelerisque pharetra orci sed viverra. Sed dictum neque eget nunc tincidunt finibus. Nam id mollis metus. Nunc auctor suscipit erat, eu viverra purus congue id. Morbi dictum augue nisi, vel rhoncus augue convallis eu. Aenean sit amet quam purus. Suspendisse congue elementum iaculis. Duis aliquet ligula ut elementum rutrum. Pellentesque et metus non justo ullamcorper fermentum. Vestibulum viverra ligula in turpis ullamcorper mattis. Proin scelerisque accumsan neque ac convallis.</p>
        <p>Aenean purus erat, blandit imperdiet lacus eu, maximus fermentum tellus. Aliquam ornare porttitor mi et mattis. Nunc ornare malesuada tincidunt. Morbi ut massa convallis, scelerisque ipsum molestie, lacinia nisi. Suspendisse potenti. Duis cursus arcu convallis dui auctor pellentesque. Fusce lacinia nunc sed lacus porta, id tempus magna iaculis. Praesent et nibh metus. Fusce tempus quis dolor molestie interdum. Praesent et quam laoreet, hendrerit mi at, pharetra purus. Maecenas quis neque sapien. Praesent pharetra sit amet tortor sit amet sodales. Nam faucibus, eros varius viverra posuere, mi neque tristique mi, sit amet commodo tellus odio ac risus. Aenean hendrerit velit tristique ultricies ornare. Sed a lectus at nisl tempor consequat sit amet vel nulla. Praesent dapibus odio eu gravida mattis. </p>
    HTML;

    private array $data;

    private PathRepository $pathRepository;

    public function __construct(
        PathRepository $pathRepository
    ) {
        $this->pathRepository = $pathRepository;
        $this->data = [
            [
                'path' => 'en/magento/installation/docker-configuration',
                'title' => 'Docker configuration',
                'summary' => $this->dumbSummary,
                'content' => '<p class="article-content-identifier">docker-configuration en</p>' . $this->dumbContent,
                'creation_date' => '2010-10-10 01:01:01',
                'update_date' => '2010-10-20 01:01:01',
            ],
            [
                'path' => 'en/magento/installation/composer',
                'title' => 'Composer',
                'summary' => $this->dumbSummary,
                'content' => '<p class="article-content-identifier">composer en</p>' . $this->dumbContent,
                'creation_date' => '2009-12-24 17:30:00',
                'update_date' => '2015-02-19 01:01:01',
            ],
            [
                'path' => 'fr/magento/installation/configuration-docker',
                'title' => 'Configuration Docker',
                'summary' => $this->dumbSummary,
                'content' => '<p class="article-content-identifier">docker-configuration fr</p>' . $this->dumbContent,
                'creation_date' => '2015-02-17 01:01:01',
                'update_date' => '2015-02-17 01:01:01',
            ],
            [
                'path' => 'fr/magento/installation/composer',
                'title' => 'Composer',
                'summary' => $this->dumbSummary,
                'content' => '<p class="article-content-identifier">composer fr</p>' . $this->dumbContent,
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [PathFixtures::class];
    }

    public function load(ObjectManager $manager) : void
    {
        foreach ($this->data as $currentData) {
            $article = new Article();
            $path = $this->pathRepository->findByPath($currentData['path']);
            $article->setPath($path);
            $article->setTitle($currentData['title']);
            $article->setSummary($currentData['summary']);
            $article->setContent($currentData['content']);
            if (isset($currentData['creation_date'])) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $currentData['creation_date']);
                $article->setCreationDate($date);
            }
            if (isset($currentData['update_date'])) {
                $date = new DateTime($currentData['update_date']);
                $article->setUpdateDate($date);
            }
            $manager->persist($article);
        }
        $manager->flush();
    }
}
