<?php

namespace App\Repositories;

use App\Common\SQLOperator;
use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    //model muốn tương tác
    protected $model;

    //khởi tạo
    public function __construct()
    {
        $this->setModel();
    }

    //lấy model tương ứng
    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function selectAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function count($modelParam = null)
    {
        if($modelParam){
            return $modelParam->count();
        }
        return $this->model->count();
    }

    public function getModelByMultiKeys(array $attribute = [[]])
    {
        $scopeModel = $this->executeWhere($attribute);
        return $scopeModel;
    }

    public function selectByMultiKeys(array $attribute = [[]])
    {
        $scopeModel = $this->executeWhere($attribute);
        return $scopeModel->get();
    }

    public function selectLimit($skip, $take, $modelParam = null)
    {
        if ($modelParam) {
            return $modelParam->limit($take)
                ->offset($skip * $take)
                ->get();
        }
        return $this->model->limit($take)
            ->offset($skip * $take)
            ->get();
    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($modelParam, $attributes = [])
    {
        $modelParam->update($attributes);
        return $modelParam;
    }

    public function updateByID($id, array $attributes = [])
    {
        $result = $this->model->where('id', $id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    /**
     * @param array $attribute
     * @return mixed
     */
    public function executeWhere(array $attribute)
    {
        $scopeModel = $this->model;
        foreach ($attribute as [$key, $operation, $value]) {
            if($value){
                switch ($operation) {
                    case SQLOperator::IN:
                        $scopeModel = $scopeModel->whereIn($key, $value);
                        break;
                    case SQLOperator::BETWEEN:
                        $scopeModel = $scopeModel->whereBetween($key, $value);
                        break;
                    case SQLOperator::LIKE:
                        $scopeModel = $scopeModel->where($key, SQLOperator::LIKE, $value);
                        break;
                    case SQLOperator::OR_IN:
                        $scopeModel = $scopeModel->orWhereIn($key, $value);
                        break;
                    case SQLOperator::OR_BETWEEN:
                        $scopeModel = $scopeModel->orWhereBetween($key, $value);
                        break;
                    case SQLOperator::OR_LIKE:
                        $scopeModel = $scopeModel->orWhere($key, SQLOperator::LIKE, $value);
                        break;
                    case SQLOperator::OR_EQUAL:
                        $scopeModel = $scopeModel->orWhere($key, $value);
                        break;
                    case SQLOperator::RAW_SQL:
                        $value = str_replace('%', '', $value);
                        if($value){
                            $scopeModel = $scopeModel->whereRaw($key, array($value));
                        }
                        break;
                    default:
                        $scopeModel = $scopeModel->where($key, $operation, $value);
                }
            }
        }
        return $scopeModel;
    }
}
