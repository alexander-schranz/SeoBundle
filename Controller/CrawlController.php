<?php

namespace L91\Bundle\SeoBundle\Controller;

use L91\Bundle\SeoBundle\Entity\Url;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CrawlController extends Controller
{
    public function overviewAction(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $limit = 20;
        $offset = $limit * ($page - 1);

        return $this->render(
            'L91SeoBundle:crawl:overview.html.twig',
            [
                'page' => $page,
                'limit' => $limit,
                'offset' => $offset,
                'crawlList' => $this->get('l91_seo.repository.crawl')->findBy([], ['id' => 'desc'], $limit, $offset),
            ]
        );
    }

    public function detailAction(Request $request, $id)
    {
        return $this->render(
            'L91SeoBundle:crawl:detail.html.twig',
            [
                'crawl' => $this->get('l91_seo.repository.crawl')->find($id),
            ]
        );
    }

    public function urlAction(Request $request, $urlId)
    {
        /** @var Url $url */
        $url = $this->get('l91_seo.repository.url')->find($urlId);

        return $this->render(
            'L91SeoBundle:crawl:url.html.twig',
            [
                'url' => $url,
                'crawl' => $url->getCrawl(),
            ]
        );
    }
}
