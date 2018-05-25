<?php

use Dawood\LaravelConfValidator\CustomValidationException;

if (! function_exists('laraConValidator'))
{
    function laraConValidator()
    {
        return "Loaded Laravel config based validator";
    }
}


if (! function_exists('validationRules'))
{
    /**
     * return the validation rules of provided type
     * from validation config
     * @param string $validationType
     * @param string|array|null $ruleKeys , rules to return
     * @param array $variables
     * @return array examples :
     * examples :
     * validationRules('release',[
     * 'file_example'=>['type','size'],
     * 'description',
     * ])
     * this will return complete description rules, file_example rules
     * are array so it will return only requested
     *
     * example 2 :
     * validationRules('release',[
     * 'file_example',
     * 'description',
     * ])
     * this will return complete description rules and of file_example
     * also as we know file_example rules are array and
     * user did't specified sub rule so we will
     * return all of it's rule
     *
     * example 3 :
     * validationRules('release')
     * this will return all rules of release in format
     * accepted by laravel validator
     *
     * example 4 :
     * validationRules('form','form_name',['FORM_NAME'=>12,'RELEASE_ID'=>12])
     * this will return all rules of form_name as following
     * required|string|regex:/^[a-z0-9\_]{1,30}$/|unique:meta_forms,form_name,null,id,release_id,51
     *
     * if you see the form rules there are two variables FORM_NAME and RELEASE_ID
     * that are being replaced by our third argument
     *
     * @author Dawood Ikhlaq <dawood.ikhlaq@nubilaria.com>
     */
    function validationRules($validationType, $ruleKeys = null, array $variables = null)
    {
        if(is_string($ruleKeys))
        {
            $rules = config("validation.$validationType.$ruleKeys");
            if(is_array($rules))
            {
                $rules = implode('|',$rules);
            }
            return replaceRuleArrayInString([$ruleKeys=>$rules],$variables);
        }
        if(!$ruleKeys)
        {
            $ruleKeys = array_keys(config("validation.$validationType"));
        }
        $validationRules = [];
        foreach ($ruleKeys as $key => $value)
        {
            if(is_string($value))
            {
                $rules = config("validation.$validationType.$value");
                if(is_array($rules))
                {
                    $rules = implode('|',$rules);
                }
                $validationRules[$value] = $rules;
                continue;
            }
            $subKeys = [];
            foreach ($value as $subKey)
            {
                if(!config("validation.$validationType.$key.$subKey"))
                {
                    $subKeys[] = $subKey;
                    continue;
                }
                $subKeys[] = config("validation.$validationType.$key.$subKey");
            }
            $validationRules[$key] = implode('|',$subKeys);
        }
        return replaceRuleArrayInString($validationRules,$variables);
    }
}

if (! function_exists('replaceRuleArrayInString'))
{
    /**
     * search variables in rule and replace with provided value in variablesToReplace
     * @param array $validationRules
     * @param array|null $variablesToReplace
     * @return array
     * @author Dawood Ikhlaq <dawood.ikhlaq@nubilaria.com>
     */
    function replaceRuleArrayInString(array $validationRules, array $variablesToReplace = null)
    {
        if(!count($variablesToReplace))
        {
            return $validationRules;
        }
        $validationRulesFinal = [];
        foreach ($validationRules as $key => $rule)
        {
            foreach ($variablesToReplace as $variable => $value)
            {
                $rule = str_replace("%$variable%",$value,$rule);
            }
            $validationRulesFinal[$key] = $rule;
        }
        return $validationRulesFinal;
    }
}


if (! function_exists('validateDataAgainstRules'))
{

    /**
     * get the validation rules of provided type
     * from validation config and validate that
     * against provided data
     * @param string $validationType
     * @param string|array|null $ruleKeys
     * @param array $data
     * @param array $variables
     * @author Dawood Ikhlaq <dawood.ikhlaq@nubilaria.com>
     */
    function validateDataAgainstRules($validationType, $ruleKeys = null, array $data, array $variables = null)
    {
        /*
         |-------------------------------
         | if DISABLE_VALIDATION env variable is
         | set and true we won't validate the
         | data against the rules
         |-------------------------------
         */
        if(env('DISABLE_VALIDATION',false))
        {
            return;
        }
        $validationRules = validationRules($validationType,$ruleKeys, $variables);
        $validator = Validator::make($data, $validationRules);
        if($validator->fails())
        {
            throw new CustomValidationException($validator);
        }
    }
}