import React from 'react'
import Form, { Field } from './Form';
import { isNil } from 'lodash';
import $ from 'jquery';

interface ErrorCodeHint {
    hint: string,
    bindField?: string
}

interface ErrorMap {
    [code: number]: ErrorCodeHint
}

interface FormProps extends React.ComponentProps<typeof Form> {
    errorMap?: ErrorMap
}

interface FormState {
    errorCodes: number[]
}

interface ActionApiResult {
    errors: number[],
    result: object,
    redirect?: string
}

class LightnessAjaxForm extends React.Component<FormProps, FormState> {
    public constructor(props: FormProps) {
        super(props);
        this.state = {
            errorCodes: []
        }
    }

    public render(): React.ReactNode {
        const decoratedProps = { ...this.props };
        if (!isNil(this.props.actionUrl)) {
            decoratedProps.actionUrl = null;
            decoratedProps.onSubmit = (event) => {
                if (!isNil(this.props.onSubmit)) this.props.onSubmit(event);
                $.ajax({
                    url: this.props.actionUrl,
                    method: this.props.method,
                    data: new FormData(event.currentTarget),
                    contentType: false,
                    dataType: 'json',
                    processData: false, // to make FormData as data work
                    success: (result: ActionApiResult) => {
                        if (!isNil(result.redirect)) { 
                            window.location.href = result.redirect;
                        } else {
                            this.setState({
                                errorCodes: result.errors
                            })
                        }
                    }
                })
            }
            if (!isNil(this.props.errorMap)) {
                const errorMap = this.props.errorMap;
                decoratedProps.errors = [
                    ...this.state.errorCodes
                        .filter((code) => {
                            return errorMap[code] !== undefined
                                && isNil(errorMap[code].bindField);
                        })
                        .map((code) => errorMap[code].hint)
                ]
                decoratedProps.fields.forEach((item) => {
                    // Item must be a field.
                    if ((item as any).name === undefined) return;
                    const field = item as Field;
                    field.errors = [
                        ...this.state.errorCodes
                            .filter((code) => {
                                return errorMap[code] !== undefined
                                    && errorMap[code].bindField === field.name
                            })
                            .map((code) => errorMap[code].hint)
                    ]
                })
            }
        };

        return <Form { ...decoratedProps } />
    }
}

export default LightnessAjaxForm;