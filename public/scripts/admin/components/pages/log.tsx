import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Table from '../table';
import Status, { Type } from '../status';
import Mark from '../mark';

interface LogRecord {
    type: string,
    ip?: string, // depends on cli flag
    cli: boolean,
    date: string,
    message: string
}

interface LogPageProps {
    records: LogRecord[],
    clearLogUrl: string
}

class LogPage extends React.Component<LogPageProps> {
    public render(): React.ReactNode {
        return <>
            <div className="content__header">
                <Breadcrumbs items={[{
                    name: 'Лог'
                }]} />
                <a href={this.props.clearLogUrl} className="button">Очистить лог</a>
            </div>
            <div className="box box--table">
                <Table
                    className="log"
                    headers={['Тип', 'IP', 'Дата']}
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
                            <span className="log__date">{record.date}</span>
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
        </>;
    }
}

export default LogPage;