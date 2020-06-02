import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Parameter from '../common/Parameter';
import Mark from '../common/Mark';
import { MarkColor } from '../common/_common';

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
    canChangeGroup: boolean,
    canUseMessages: boolean
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
                                color={MarkColor.GREEN}
                            />
                            : <Mark
                                className="profile__status"
                                label="Offline"
                                color={MarkColor.RED}
                            />
                        }
                    </div>
                </div>
                <div className="box">
                    <div className="profile__box">
                        <img src={this.props.user.avatarUrl} className="profile__avatar" />
                        <div className="profile__data">
                            <Parameter
                                name="Логин"
                                value={this.props.user.login}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Имя"
                                value={this.props.user.name}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Фамилия"
                                value={this.props.user.surname}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Пол"
                                value={this.props.user.gender}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Email"
                                value={this.props.user.email}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Дата регистрации"
                                value={this.props.user.registrationDate}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Последний раз онлайн"
                                value={this.props.user.lastOnlineTime || 'Никогда'}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Последнее устройство"
                                value={this.props.user.lastUserAgent}
                                nameIsStrong={true}
                                divisor=": "
                            />
                            <Parameter
                                name="Группа"
                                value={this.props.user.group}
                                nameIsStrong={true}
                                divisor=": "
                            />
                        </div>
                    </div>
                    {(this.props.rights.canChangeGroup || this.props.rights.canEdit || this.props.rights.canUseMessages) &&
                        <div className="box__actions box-actions">
                            {this.props.rights.canUseMessages &&
                                <a href={`/admin/profile/dialog?uid=${this.props.user.id}`} className="box-actions__item">
                                    <i className="box-actions__icon icon-email"></i>Новое сообщение
                                </a>
                            }
                            {this.props.rights.canChangeGroup &&
                                <a href={`/admin/users/change/group?id=${this.props.user.id}`} className="box-actions__item">
                                    <i className="box-actions__icon icon-group"></i>Изменить группу
                                </a>
                            }
                            {this.props.rights.canEdit && <>
                                <a href={`/admin/users/edit/profile?id=${this.props.user.id}`} className="box-actions__item">
                                    <i className="box-actions__icon icon-pencil"></i>Редактировать профиль
                                </a>
                                {this.props.user.hasAvatar &&
                                    <a
                                        href={this.props.actions.deleteAvatarUrl}
                                        className="box-actions__item box-actions__item--red"
                                    >
                                        <i className="box-actions__icon icon-trash"></i>
                                        Удалить аватар
                                    </a>
                                }
                            </>}
                        </div>
                    }
                </div>
            </div>
        );
    }
}

export default ProfilePage;