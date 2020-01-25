import React from 'react';

export interface DetailsProps {
    title?: string,
    content: any
}

const Details = function(props: DetailsProps) {
    return (
        <div className="details">
            {props.title &&
                <span className="details__header">
                    {props.title}
                </span>
            }
            <div className="details__content">
                {props.content}
            </div>
        </div>
    )
}

export default Details;