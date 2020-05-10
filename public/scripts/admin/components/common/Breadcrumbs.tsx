import React from 'react';
import { isNil } from 'lodash';
import classNames from 'classnames';

export interface BreadcrumbsItem {
    name: string,
    link?: string
}

interface BreadcrumbsProps {
    items: BreadcrumbsItem[]
}

class Breadcrumbs extends React.Component<BreadcrumbsProps> {
    public render(): React.ReactNode {
        return (
            <div className="breadcrumbs">
                {this.props.items.map((item, index) => (<span key={index}>
                    <a 
                        href={!isNil(item.link) ? item.link : null}
                        className={classNames(
                            'breadcrumbs__item',
                            {'breadcrumbs__item--link': !isNil(item.link)},
                            {'breadcrumbs__item--current': 
                                index === (this.props.items.length - 1)
                            }
                        )}
                    >{item.name}</a>
                    {index !== (this.props.items.length - 1) && 
                        <>
                            &nbsp;
                            <span className="breadcrumbs__divisor"></span>
                            &nbsp;
                        </>
                    }
                </span>))}
            </div>
        )
    }
}

export default Breadcrumbs;