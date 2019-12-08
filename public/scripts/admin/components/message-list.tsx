import React from 'react'

class MessageList extends React.Component {
    private textAreaIsEmpty = true;
    
    public constructor(props) {
        super(props);

        this.handleTextAreaFocus = this.handleTextAreaFocus.bind(this);
        this.handleTextAreaBlur = this.handleTextAreaBlur.bind(this);
    }

    public render(): React.ReactNode {
        return (
        <div className="content__column">
            <div className="box">
                <form action="" className="box-form">
                    <span className="box-form__title">Новое сообщение</span>
                    <div 
                        contentEditable={true}
                        className="box-form__textarea" 
                        onChange={this.handleTextChange}
                        onFocus={this.handleTextAreaFocus}
                        onBlur={this.handleTextAreaBlur}
                        data-placeholdered
                    >Текст сообщения</div>
                    <button className="box-form__button">Отправить
                        <i className="box-form__button-icon fontello icon-ok"></i></button>
                </form>
            </div>
            <div className="box">
                <span className="notice">Сообщений с пользователем
                    <span className="notice__strong">?= $with->login ?></span>
                    пока нет</span>
                <div className="messages">
                    <div className="messages__item message">
                        <div className="message__header">
                            <img src="/<?= $with->getAvatarUrl() ?>" className="message__from-avatar"/>
                                <div className="message__info">
                                    <span className="message__from-login">Some User</span>
                                    <span className="message__date">07.12.2019 11:12</span>
                                </div>
                                <div className="message__status message__status--active">
                                    <i className="message__status-icon fontello icon-ok"></i>
                                    <span className="message__status-text">Отправлено</span>
                                </div>
                        </div>
                        <span className="message__text">Some message text here</span>
                    </div>
                </div>
            </div>
        </div>);
    }

    private handleTextChange(event: React.ChangeEvent<HTMLDivElement>) {
        // const textarea = event.target;
        // textarea.style.height = textarea.scrollHeight + 'px';
    }

    private handleTextAreaFocus(event: React.FocusEvent<HTMLDivElement>) {
        if (this.textAreaIsEmpty) {
            event.target.textContent = "";
            event.target.removeAttribute('data-placeholdered');
        }
    }

    private handleTextAreaBlur(event: React.FocusEvent<HTMLDivElement>) {
        if (event.target.textContent === "") {
            this.textAreaIsEmpty = true;
            event.target.textContent = "Текст сообщения";
            event.target.setAttribute('data-placeholdered', '');
        } else {
            this.textAreaIsEmpty = false;
        }
    }
}

export default MessageList;