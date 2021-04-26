<?php

namespace Zelenin\SmsRu\Response;

class SmsResponse extends AbstractResponse
{

    /**
     * @var integer[]
     */
    public $ids = [];

    /**
     * @var array
     */
    protected $availableDescriptions = [
        '100' => '��������� ������� � ��������. �� ��������� �������� �� ������� �������������� ������������ ��������� � ��� �� �������, � ������� �� ������� ������, �� ������� ����������� ��������.',
        '200' => '������������ api_id.',
        '201' => '�� ������� ������� �� ������� �����.',
        '202' => '����������� ������ ����������.',
        '203' => '��� ������ ���������.',
        '204' => '��� ����������� �� ����������� � ��������������.',
        '205' => '��������� ������� ������� (��������� 8 ���).',
        '206' => '����� �������� ��� ��� �������� ������� ����� �� �������� ���������.',
        '207' => '�� ���� ����� (��� ���� �� �������) ������ ���������� ���������, ���� ������� ����� 100 ������� � ������ �����������.',
        '208' => '�������� time ������ �����������.',
        '209' => '�� �������� ���� ����� (��� ���� �� �������) � ����-����.',
        '210' => '������������ GET, ��� ���������� ������������ POST.',
        '211' => '����� �� ������.',
        '212' => '����� ��������� ���������� �������� � ��������� UTF-8 (�� �������� � ������ ���������).',
        '220' => '������ �������� ����������, ���������� ���� �����.',
        '230' => '��������� �� ������� � ��������, ��� ��� �� ���� ����� � ���� ������ ���������� ����� 60 ���������.',
        '300' => '������������ token (�������� ����� ���� ��������, ���� ��� IP ���������).',
        '301' => '������������ ������, ���� ������������ �� ������.',
        '302' => '������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���).',
    ];
}
