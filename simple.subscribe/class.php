<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !==true) {
    die();
}

use \Bitrix\Main\Engine;
use \Bitrix\Main\Result;
use \Bitrix\Main\Error;

class FormSubscribeComponent
    extends \CBitrixComponent 
    implements Engine\Contract\Controllerable
{
    private $GROUP_SUBSCRIBE_CODE = 'subscribers';
    private $GROUP_SUBSCRIBE_ID = null;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    protected function listKeysSignedParameters() 
    {
        return [
            'SUCCESS_MESSAGE',
            'EVENT_TYPE',
        ];
    }

    public function configureActions()
    {
        return [
            'submit' => [
                'prefilters' => [
                    new Engine\ActionFilter\Csrf
                ]
            ],
        ];
    }

    public function submitAction($form)
    {
        $result = new Result();
        if (empty(trim($form['EMAIL'])))
        {
            $result->addError(
                new Error('Поле "Email" обязательное для заполнения')
            );
        }

        if (!$this->GROUP_SUBSCRIBE_ID) {
            $result->addError(
                new Error('Не указана группа для добавления пользователей')
            );
        }

        $resultUser = $this->handlerUser($form);

        if ($resultUser['STATUS'] == 'success') {
            $arFields = [
                'NAME'    => htmlspecialcharsbx($form['NAME']),
                'EMAIL'   => htmlspecialcharsbx($form['EMAIL']),
            ];
            $eventType = $this->arParams['EVENT_TYPE'];
            $eventId = \CEvent::Send($eventType, SITE_ID, $arFields);

            if (empty($eventId)) {
                $result->addError(
                    new Error('Произошла ошибка. Повторие ваш запрос позже.')
                );
                return Engine\Response\AjaxJson::createError($result->getErrorCollection(), []);
            }
        }

        return [
            'message' => $resultUser['MESSAGE'],
        ];
    }

    private function getSubscribGroupCode()
    {
        $sibsctibeId = \Bitrix\Main\GroupTable::getList(array(
            'select'  => [
                'ID',
            ],
            'filter'  => [
                '!ID'=>'1',
                'STRING_ID' => $this->GROUP_SUBSCRIBE_CODE,
            ]
        ))->fetch()['ID'];		
        
        if (intval($sibsctibeId) > 0) {
            $this->GROUP_SUBSCRIBE_ID = $sibsctibeId;
        }
    }

    private function handlerUser ($arParams)
    {
        $this->getSubscribGroupCode();

        $arReturn = [
            'STATUS' => 'error',
            'MESSAGE' => 'Ошибка подписки',
        ];

        $arUser = \Bitrix\Main\UserTable::getList([
            'filter' => [
                'LOGIC'=>'OR',
                ['EMAIL' => $arParams['EMAIL']],
                ['LOGIN' => $arParams['EMAIL']],
            ],
            'select' => [
                '*',
            ],
        ])->fetch();

        if (intval($arUser['ID']) > 0) {
            $userGroups = \Bitrix\Main\UserTable::getUserGroupIds($arUser['ID']);
            if (in_array($this->GROUP_SUBSCRIBE_ID, $userGroups)) {
                $arReturn['STATUS'] = 'exists';
                $arReturn['MESSAGE'] = 'Пользователь уже подписан';
            } else {
                \Bitrix\Main\UserGroupTable::add([
                    'USER_ID' => $arUser['ID'],
                    'GROUP_ID' => $this->GROUP_SUBSCRIBE_ID,
                ]);
                $arReturn['STATUS'] = 'success';
                $arReturn['MESSAGE'] = 'Подписка успешно оформлена';
            }
        } else {
            $arReturn = $this->createUser($arParams);
        }

        return $arReturn;
    }

    private function createUser($arParams)
    {
        $result = [];
        $password = randString(7);
        $user = new CUser;
        $arFields = [
            'NAME' => $arParams['NAME'],
            'EMAIL' => $arParams['EMAIL'],
            'LOGIN' => $arParams['EMAIL'],
            'LID' => 'ru',
            'ACTIVE' => 'Y',
            'GROUP_ID' => [$this->GROUP_SUBSCRIBE_ID],
            'PASSWORD' => $password,
            'CONFIRM_PASSWORD' => $password,
        ];
        $userId = $user->Add($arFields);
        if (intval($userId) > 0) {
            $result['STATUS'] = 'success';
            $result['MESSAGE'] = 'Подписка успешно оформлена';
        } else {
            $result['MESSAGE'] = $user->LAST_ERROR;
        }

        return $result;
    }

    public function executeComponent()
    {	
        if ($this->StartResultCache(false)) {
            $this->IncludeComponentTemplate();
        }
    }
}