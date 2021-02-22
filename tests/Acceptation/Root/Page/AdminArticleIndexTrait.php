<?php

namespace App\Tests\Acceptation\Root\Page;

trait AdminArticleIndexTrait
{
    /**
     * This array represents the "title" column of the 4 first article entries that should be displayed in the listing
     * if they are sorted by ID. We use them for defining that sorts correctly work.
     *
     * @var array|string[]
     */
    private array $articleRowMapping = [
        'fr/magento/installation/composer [Composer]',
        'fr/magento/installation/configuration-docker [Configuration Docker]',
        'en/magento/installation/composer [Composer]',
        'en/magento/installation/docker-configuration [Docker configuration]',
        'fr/linux/theorie/histoire-de-la-creation [L\'histoire de la création de Linux]',
        'fr/magento/utilisation-du-cms/tout-a-propos-des-clients [Tout à propos des clients]',
        'en/magento/use-of-the-cms/all-about-customers [All about customers]'
    ];

    public function testLoginProtected()
    {
        $client = $this->getBrowser();
        $crawler = $client->request('GET', '/admin/article');
        $url = $this->getAppUrl('/en/login');
        $this->assertEquals(
            $url,
            $crawler->getUri(),
            'Response is a redirection to the /en/login page.'
        );
    }

    public function testArticlesDisplayedByDefaultOrder()
    {
        $this->checkColumnSort(null, '', [6, 5, 4, 0, 1, 2, 3]);
    }

    public function testArticlesDisplayedById()
    {
        $this->checkColumnSort('ID', '?sort=id', [6, 5, 4, 0, 1, 2, 3]);
    }

    public function testArticlesDisplayedByPath()
    {
        $this->checkColumnSort('Path', '?sort=path', [2, 3, 6, 4, 0, 1, 5]);
    }

    public function testArticlesDisplayedByCreationDate()
    {
        $this->checkColumnSort('Creation date', '?sort=creation_date', [6, 5, 0, 4, 1, 3, 2]);
    }

    public function testArticleDisplayedByUpdateDate()
    {
        $this->checkColumnSort('Last update', '?sort=update_date', [6, 5, 4, 0, 2, 1, 3]);
    }

    public function testCheckUpdateLink()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client = $this->getBrowser();
        $client->request('GET', '/admin/article');
        $updateButtons = $this->getAllElementsByCssSelector('table tr td.actions .edition');
        $updateButtons[0]->click();
        $url = $this->getAppUrl('/admin/article/edit/7');
        $this->assertEquals(
            $url,
            $this->getBrowser()->getWebDriver()->getCurrentUrl(),
            'A click on the "edition" link of an article leads to the correct page.'
        );
    }

    protected function checkColumnSort(?string $linkTextToClickForTheSort, string $expectedGetParam, array $expectedOrder)
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->getBrowser()->request('GET', '/admin/article');
        $expectedUrl = $this->getAppUrl("/admin/article$expectedGetParam");
        if ($linkTextToClickForTheSort) {
            $column = $this->getElementByLinkText($linkTextToClickForTheSort);
            $column->click();
        }
        $this->assertEquals(
            $expectedUrl,
            $this->getBrowser()->getWebDriver()->getCurrentUrl(),
            'The "sort" GET parameter is wrong.'
        );
        $articleRows = $this->getAllElementsByCssSelector('table tr td:nth-child(2)');
        $i = 0;
        foreach ($articleRows as $currentRow) {
            if ($i > 3) {
                continue;
            }
            $this->assertEquals(
                $this->articleRowMapping[$expectedOrder[$i]],
                $currentRow->getText(),
                "The current row is at the good position with articles sorted by $linkTextToClickForTheSort (iteration: $i)."
            );
            $i++;
        }
    }
}
