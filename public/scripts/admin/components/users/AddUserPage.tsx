import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import { TextField, RadioField, RadioValue, FileField } from '../form/Form';
import LightnessAjaxForm from '../form/LightnessAjaxForm';

interface AddUserPageProps {
    form: {
        url: string,
        genders: {
            values: RadioValue[],
            currentValue: string
        }
        errorMap: typeof LightnessAjaxForm.prototype.props.errorMap
    }
}

class AddUserPage extends React.Component<AddUserPageProps> {
    public render(): React.ReactNode {
        const props = this.props;
        return (<>
            <div className="content__header">
                <Breadcrumbs items={[{
                    name: 'Пользователи',
                    link: '/admin/users'
                }, {
                    name: 'Добавить'
                }]} />
            </div>
            <div className="box">
                <LightnessAjaxForm
                    actionUrl={props.form.url}
                    method="post"
                    multipart={true}
                    fields={[{
                        type: 'text',
                        title: 'Логин',
                        name: 'login'
                    } as TextField, {
                        type: 'password',
                        title: 'Пароль',
                        name: 'password'
                    } as TextField, {
                        type: 'text',
                        title: 'Email',
                        name: 'email'
                    } as TextField, {
                        type: 'text',
                        title: 'Имя',
                        name: 'name'
                    } as TextField, {
                        type: 'text',
                        title: 'Фамилия',
                        name: 'surname'
                    } as TextField, {
                        type: 'radio',
                        title: 'Пол',
                        name: 'gender_id',
                        values: this.props.form.genders.values,
                        currentValue: this.props.form.genders.currentValue
                    } as RadioField, {
                        type: 'file',
                        title: 'Аватар',
                        name: 'avatar',
                    } as FileField]}
                    buttonText="Добавить"
                    short={true}
                    errorMap={this.props.form.errorMap}
                />
            </div>
        </>);
    }
}

export default AddUserPage;