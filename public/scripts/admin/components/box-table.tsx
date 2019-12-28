import React from 'react';

export interface ItemDetails {
    title?: string,
    content: any
}

export interface TableItem {
    cells: any[]
    details?: ItemDetails[]
}

interface TableProps {
    className?: string
    headers?: string[],
    items: TableItem[]
}

class BoxTable extends React.Component<TableProps> {
    public render(): React.ReactNode {
        const className = this.props.className ? ' ' + this.props.className : '';
        return (
            <div className={"box box--table" + className}>
                <table className="table">
                    {this.props.headers && this.props.headers.length &&
                        <thead>
                            <tr className="table__headers">
                                {this.props.headers.map((name, index) => 
                                    <td key={index} className="table__header">
                                        {name}
                                    </td>
                                )}
                            </tr>
                        </thead>
                    }
                    {this.props.items.map((item, index) => 
                        <tbody key={index} className="table__item-wrapper">
                            <tr className="table__item">
                                {item.cells.map((value, index) => 
                                    <td key={index} className="table__cell">
                                        {value}
                                    </td>
                                )}
                            </tr>
                            {item.details &&
                                <tr className="table__details-wrapper">
                                    <td className="table__details" colSpan={100}>
                                        {item.details.map((details, index) => 
                                            <div key={index} className="details">
                                                {details.title &&
                                                    <span className="details__header">
                                                        {details.title}
                                                    </span>
                                                }
                                                {details.content}
                                            </div>
                                        )}
                                    </td>
                                </tr>
                            }
                        </tbody>
                    )}
                </table>
            </div>
        );
    }
}

export default BoxTable;