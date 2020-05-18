import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Table from '../table/Table';
import Status, { Type } from '../common/Status';
import Mark from '../common/Mark';
import classNames from 'classnames';
import ContentHeader from '../content/ContentHeader';

interface LogRecord {
    type: string,
    ip?: string, // depends on cli flag
    cli: boolean,
    time: string,
    message: string
}

interface Props {
    date: string,
    records: LogRecord[],
    readedRecords: number,
}

class LogPage extends React.Component<Props> {
    public render(): React.ReactNode {
        const unreadedRecords = this.props.records.length - this.props.readedRecords;
        return (
            <div className="log">
                <ContentHeader>
                    <div className="breadcrumbs-wrapper">
                        <Breadcrumbs items={[{
                            name: `Лог ${this.props.date} (${this.props.records.length})`
                        }]} />
                        <span className={classNames(
                            "content__count",
                            { "content__count--yellow": unreadedRecords }
                        )}>
                            <i className="icon-flash-1"></i>
                            {unreadedRecords}
                        </span>
                    </div>
                </ContentHeader>
                <div className="box box--table">
                    <Table
                        headers={['Тип', 'IP', 'Время']}
                        items={this.props.records.map((record, index) => ({
                            cells: [
                                <Mark 
                                    className="log__type"
                                    label={record.type} 
                                    color={(() => {
                                        switch (record.type) {
                                            case 'Emergency':
                                            case 'Critical':
                                            case 'Error': return 'red';
                                            case 'Alert':
                                            case 'Warning': return 'yellow';
                                            case 'Notice': return 'blue';
                                            case 'Info': return 'green';
                                            case 'Debug':
                                            case 'Testing': return 'purple';
                                            default: return 'grey';
                                        }
                                    })()}
                                    icon={(() => {
                                        switch (record.type) {
                                            case 'Emergency': return 'clock';
                                            case 'Critical': return 'flash-1';
                                            case 'Alert': return 'alert';
                                            case 'Error': return 'cancel-circle';
                                            case 'Warning': return 'attention';
                                            case 'Notice': return 'bell-alt';
                                            case 'Info': return 'info-circled';
                                            case 'Debug': return 'bug';
                                            case 'Testing': return 'flag';
                                            default: return '';
                                        }
                                    })()}
                                />,
                                record.cli
                                    ? <Status type={Type.NONE} message="From CLI" />
                                    : record.ip,
                                <>
                                    <span className="log__date">{record.time}</span>
                                    &nbsp;
                                    {index + 1 > this.props.readedRecords
                                        ? <span className="mark mark--red">
                                            Unreaded
                                        </span>
                                        : <span className="mark mark--grey">
                                            Readed
                                        </span>
                                    }
                                </>
                            ],
                            details: [{
                                content: (() => {
                                    let messageParts = record.message.split('\n');
                                    const header = messageParts[0];
                                    messageParts.shift();
                                    const message = messageParts.join('\n');
                                    return (
                                        <p className="log__message">
                                            <p className="log__message--header">
                                                {header}
                                            </p>
                                            {messageParts.length !== 1 && message}
                                        </p>
                                    )
                                })()
                            }]
                        }))}
                    />
                </div>
            </div>
        );
    }
}

export default LogPage;