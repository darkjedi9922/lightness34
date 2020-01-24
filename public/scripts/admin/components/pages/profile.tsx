import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Parameter from '../parameter';
import classNames from 'classnames';
import Status, { Type } from '../status';
import Mark from '../mark';

interface User {
    id: number,
    login: string,
    hasAvatar: boolean,
    avatarUrl: string,
    name: string,
    surname: string,
    gender: string,
    email: string,
    group: string,
    registrationDate: string,
    lastOnlineTime?: string,
    lastUserAgent: string,
    isOnline: boolean
}

interface PageRights {
    canEdit: boolean,
    canChangeGroup: boolean
}

interface Actions {
    deleteAvatarUrl: string
}

interface ProfileProps {
    user: User,
    rights: PageRights,
    actions: Actions
}

class ProfilePage extends React.Component<ProfileProps> {
    public render(): React.ReactNode {
        return (
            <div className="profile">
                <div className="content__header">
                    <div className="breadcrumbs-wrapper">
                        <Breadcrumbs items={[{
                            name: "Пользователи",
                            link: '/admin/users'
                        }, {
                            name: `ID ${this.props.user.id}`
                        }]} />
                        {this.props.user.isOnline
                            ? <Mark
                                className="profile__status"
                                label="Online"
                                color="green"
                            />
                            : <Mark
                                className="profile__status"
                                label="Offline"
                                color="red"
                            />
                        }
                    </div>
                    <div className="actions">
                        {this.props.rights.canChangeGroup &&
                            <a
                                href={`/admin/users/change/group?id=${this.props.user.id}`}
                                className="button actions__item"
                            >
                                <i className="button__icon icon-group"></i>
                                Изменить группу
                            </a>
                        }
                        {this.props.rights.canEdit && <>
                            <a
                                href={`/admin/users/edit/profile?id=${this.props.user.id}`}
                                className="button actions__item"
                            >
                                <i className="button__icon icon-pencil"></i>
                                Редактировать
                            </a>
                            {this.props.user.hasAvatar &&
                                <a
                                    className="button button--red actions__item"
                                    href={this.props.actions.deleteAvatarUrl}
                                >
                                    <i className="button__icon icon-trash"></i>
                                    Удалить аватар
                                </a>
                            }
                        </>}
                    </div>
                </div>
                <div className="box profile__box">
                    <img src={this.props.user.avatarUrl} className="profile__avatar"/>
                    <div className="profile__data">
                        <Parameter 
                            name="Логин"
                            value={this.props.user.login}
                        />
                        <Parameter
                            name="Имя"
                            value={this.props.user.name}
                        />
                        <Parameter
                            name="Фамилия"
                            value={this.props.user.surname}
                        />
                        <Parameter 
                            name="Пол"
                            value={this.props.user.gender}
                        />
                        <Parameter
                            name="Email"
                            value={this.props.user.email}
                        />
                        <Parameter
                            name="Дата регистрации"
                            value={this.props.user.registrationDate}
                        />
                        <Parameter
                            name="Последний раз онлайн"
                            value={this.props.user.lastOnlineTime || 'Никогда'}/>
                        <Parameter name="Последнее устройство"
                            value={this.props.user.lastUserAgent}
                        />
                        <Parameter
                            name="Группа"
                            value={this.props.user.group}
                        />
                    </div>
                </div>
            </div>
        );
    }
}

export default ProfilePage;