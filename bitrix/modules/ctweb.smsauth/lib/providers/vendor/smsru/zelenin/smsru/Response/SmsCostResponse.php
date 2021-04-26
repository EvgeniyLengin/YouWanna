<?php

namespace Zelenin\SmsRu\Response;

class SmsCostResponse extends AbstractResponse
{

    /**
     * @var float
     */
    public $price;

    /**
     * @var int
     */
    public $length;

    /**
     * @var array
     */
    protected $availableDescriptions = [
        '100' => '������ ��������. �� ������ ������� ����� ������� ��������� ���������. �� ������� ������� ����� ������� ��� �����.',
        '200' => '������������ api_id.',
        '202' => '����������� ������ ����������.',
        '207' => '�� ���� ����� ������ ���������� ���������.',
        '210' => '������������ GET, ��� ���������� ������������ POST.',
        '211' => '����� �� ������.',
        '220' => '������ �������� ����������, ���������� ���� �����.',
        '300' => '������������ token (�������� ����� ���� ��������, ���� ��� IP ���������).',
        '301' => '������������ ������, ���� ������������ �� ������.',
        '302' => '������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���).',
    ];
}