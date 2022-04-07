<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Tag;
use craft\web\Controller;
use percipiolondon\attendees\models\Tag as TagModel;

use Craft;

class TagController extends Controller
{
    protected $allowAnonymous = ['fetch-tags'];

    public function actionFetchTags()
    {
        $this->requireLogin();

        $request = Craft::$app->getRequest();

        $tagModel = new TagModel();
        $tagModel->tag = $request->getBodyParam('tag');

        $tagModel->validate();

        $tags = Tag::find()
            ->search('"'.$tagModel->tag.'"')
            ->all();

        $response = (object)[
            "success" => $tagModel->validate(),
            "errors" => $tagModel->getErrors(),
            "tags" => $tags
        ];

        return $this->asJson($response);
    }
}
