import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';

interface User {
    id: number,
    login: number,
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

class Profile extends React.Component<ProfileProps> {
    public render(): React.ReactNode {
        return <>
            <Breadcrumbs items={[{
                name: "Пользователи",
                link: '/admin/users'
            }, {
                name: `ID ${this.props.user.id}`
            }]} />
            <div className="box">
                <div style={{
                        float: 'left',
                        marginRight: '1%',
                        maxWidth: '40%'
                }}>
                    <img src="/<?= $profile->getAvatarUrl() ?>" style={{
                        maxWidth: '100%'
                    }} />
                    {this.props.rights.canEdit && <>
                        {this.props.user.hasAvatar && <>
                            <br/>
                            <a 
                                className="link"
                                href={this.props.actions.deleteAvatarUrl}
                            >Удалить аватар</a>
                        </>}
                        <br/>
                        <a
                            className="link"
                            href={`/admin/users/edit/profile?id=${this.props.user.id}`}
                        >Редактировать профиль</a>
                    </>}
                </div>
                <div>
                    Логин: <b>{this.props.user.login}</b>
                    {(this.props.user.name || this.props.user.surname) && <>
                        <br/>Имя: {this.props.user.name} {this.props.user.surname}
                    </>}
                    <br/>
                    Пол: {this.props.user.gender}
                    {this.props.user.email && <>
                        <br/>E-mail: {this.props.user.email}
                    </>}
                    <br/>
                    Дата регистрации: {this.props.user.registrationDate}
                    <br/>
                    Последний раз онлайн: {this.props.user.lastOnlineTime
                        ? this.props.user.lastOnlineTime
                        : "Никогда"
                    }
                    <br/>
                    Последнее устройство: {this.props.user.lastUserAgent}
                    <br/>
                    Группа: {this.props.user.group}
                    {this.props.rights.canChangeGroup &&
                        <a
                            className="link"
                            href={`/admin/users/change/group?id=${this.props.user.id}`}
                        >[Изменить]</a>
                    }
                    <br/>
                        Статус: {this.props.user.isOnline 
                            ? <span style={{color: 'green'}}>Онлайн</span>
                            : <span style={{color: 'red'}}>Оффлайн</span>
                        }
                </div>
            </div>
        </>;
    }
}

export default Profile;