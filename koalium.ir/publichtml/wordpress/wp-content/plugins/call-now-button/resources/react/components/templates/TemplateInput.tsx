import { useRef, useState, Fragment } from '@wordpress/element'
import {Template, TemplateMetadata} from "../../types/types"
import TemplateCard from "./TemplateCard";
import {ActionTypes, DisplayModes} from "../../types/aux-types";
import {CnbDomain} from "../../types/cnb-web-types";

const removeEmpty = (obj) => {
    if (!obj) return obj
    return Object.fromEntries(
        Object.entries(obj)
            .filter(([_, v]) => v != null)
            .map(([k, v]) => [k, v === Object(v) ? removeEmpty(v) : v])
    );
}

type TemplateInputPros = {
    template: Template
    setTemplate: (template: Template) => void
    nonce: string,
    ajaxUrl: string
    types: ActionTypes
    displayModes: DisplayModes
    domain: CnbDomain
    upgradeLink: string
}

const TemplateInput = ({template, setTemplate, nonce, ajaxUrl, types, displayModes, domain, upgradeLink}: TemplateInputPros) => {
    const rootRef = useRef()
    const [info, setInfo] = useState<string | undefined>(undefined)
    const [infoType, setInfoType] = useState<string | undefined>(undefined)
    const [creating, setCreating] = useState<boolean>(false)
    const [redirectUrl, setRedirectUrl] = useState<string | undefined>(undefined)

    const showProWarning = template.categories.includes("pro") && domain?.type !== "PRO"
    const handleSubmit = (e) => {
        // Take over submitting
        e.preventDefault()

        const data = {
            'action': 'cnb_create_button',
            '_wpnonce_button': nonce,
            'button': removeEmpty({...template.button, id: undefined, actions: undefined, conditions: undefined}),
            'actions': removeEmpty(template.button.actions.map((action) => {return {...action, id: undefined}})),
            'conditions': removeEmpty(template.button.conditions?.map((condition) => {return {...condition, id: null}})),
        }

        // Find out if everything is valid
        const formValid = e.target.checkValidity()
        if (!formValid) {
            setInfoType("warning")
            setInfo("Please fill out all the fields...")
            setCreating(false)

            return false
        }

        // @ts-ignore
        jQuery.post(ajaxUrl, data)
            .done((response) => {
                setInfoType("success")
                setInfo("Redirecting to your button...")
                setRedirectUrl(response.redirect_link)
                setCreating(false)

                // Redirect after 1 sec
                setTimeout(() => window.location = response.redirect_link, 1000)
            })
            .fail((error) => {
                setInfoType("error")
                setInfo("Something went wrong: " + error)
                setCreating(false)
            })

        setInfoType("info")
        setInfo("Your button is being created...")
        setCreating(true)

        // To prevent *else* from happening
        return false
    }

    const onFieldChange = (input, field, action) => {
        action[field.name] = input.target.value
        setTemplate({...template})
    }

    const getFieldsForMetadata = (metadata: TemplateMetadata) => {
        return metadata.fields.map((field) => {
            // Find Action that belongs to this metadata by ID
            const action = template.button.actions.find((action) => action.id === metadata.id)
            // Find fields
            const actionField = action[field.name] as string
            const key = metadata.id + "-" + field.name

            return <tr key={key}>
                <th>{field.line}</th>
                <td>
                    <input
                        onChange={(input) => onFieldChange(input, field, action)}
                        type={field.type === "editable" ? "text" : ""}
                        name={field.name}
                        value={actionField}
                        required={field.required}
                    />
                    <p className="description">{field.description}</p>
                </td>
            </tr>
        })
    }

    const metadataFields =
        template.metadata.map((metadata) => {
            const fields = getFieldsForMetadata(metadata)
            return <Fragment key={metadata.id}>
                {metadata.title && <tr>
                    <th colSpan={2}>
                        <h3>{metadata.title}</h3>
                    </th>
                </tr>}
                {fields}
            </Fragment>
        })

    const buttonTitle = template.button.actions.length > 1 ? "Generate buttons" : "Generate button"

    return (
        <div>
            {!creating && !redirectUrl && <button className="button button-secondary" onClick={() => setTemplate(undefined)}>Back to the templates</button>}
            <h2 ref={rootRef}>Configure template <code>{template.name}</code></h2>
            <TemplateCard template={template} types={types} displayModes={displayModes} handleSelect={() => {}} />
            {showProWarning && <div className="notice notice-inline notice-warning">
                <h4>This template uses <span className="cnb-pro-badge">Pro</span> features.</h4>
                {template.proFeatures && <p>{template.proFeatures}</p>}
                {upgradeLink && <p>Start your <strong>14 day free trial</strong> to see this in action! <a className="button button-primary button-small" href={upgradeLink}>Upgrade now</a></p>}
            </div>}
            <form onSubmit={handleSubmit}>
            <table className="form-table form-table-gallery">
            {metadataFields}
            </table>

            { !creating && !redirectUrl && <button type="submit" className="button button-primary">{buttonTitle}</button>}
            { creating && <button className="button button-primary components-button is-busy">Generating your button...</button>}
            { redirectUrl && <a className="button button-primary" href={redirectUrl}>Go to your new Button</a>}

            {info && <div className={"notice notice-inline notice-" + infoType}><p>{info}</p></div>}
            </form>
        </div>
    )
}

export default TemplateInput
