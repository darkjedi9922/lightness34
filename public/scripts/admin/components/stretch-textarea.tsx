import React from 'react';

interface StretchTextareaProps {
    placeholder?: string,
    className?: string
}

class StretchTextarea extends React.Component<StretchTextareaProps> {
    private textAreaIsEmpty: boolean = true;
    private rootRef = React.createRef<HTMLDivElement>();

    public constructor(props) {
        super(props);

        this.handleTextAreaFocus = this.handleTextAreaFocus.bind(this);
        this.handleTextAreaBlur = this.handleTextAreaBlur.bind(this);
    }

    public getText(): string {
        return this.rootRef.current.innerText;
    }

    public render(): React.ReactNode {
        return (
            <div ref={this.rootRef}
                contentEditable={true}
                className={this.props.className}
                onFocus={this.handleTextAreaFocus}
                onBlur={this.handleTextAreaBlur}
                suppressContentEditableWarning={true}
                data-placeholdered
            >{this.props.placeholder}</div>
        );
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

export default StretchTextarea;