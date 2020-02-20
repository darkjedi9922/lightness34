import React from 'react';

interface LoadingContentProps {
    children?: any
}

export default function LoadingContent(props: LoadingContentProps) {
    return <div className="content__clear-bckg">
        {props.children || 
            <div className="centered-wrapper centered-wrapper--fixed">
                <i className="icon-spin1 animate-spin content__loading" />
            </div>
        }
    </div>
}