import {CnbButton} from "./cnb-web-types"

export type Template = {
    /**
     * @var string
     */
    id: string
    /**
     * @var string
     */
    name: string
    /**
     * @var string[]
     */
    categories: string[]
    /**
     * URL of an image
     * @var string
     */
    image: string
    /**
     * Alt text for the image
     * @var string
     */
    image_alt: string
    /**
     * @var string
     */
    description: string
    /**
     * In case this template contains PRO elements, this explains what
     * happens if the current domain is not a PRO domain.
     *
     * @var string
     */
    proFeatures: string
    /**
     * @var CnbButton
     */
    button: CnbButton
    /**
     * @var TemplateMetadata[]
     */
    metadata: TemplateMetadata[]
}

type TemplateMetadata = {
    /**
     * ID (or: Field name / selector), should be unique across all buttons/actions
     *
     * @var string
     */
    id: string

    /**
     * A header for the template metadata "group"
     *
     * @var string
     */
    title: string

    /**
     * @var TemplateMetadataField[]
     */
    fields: TemplateMetadataField[]
}

type TemplateMetadataField = {
    /**
     * name of the field ("actionValue",etc)
     * @var string
     */
    name: string
    /**
     * editable, readonly, hidden
     *
     * @var string
     */
    type: "editable" | "readonly" | "hidden"

    /**
     * shown as context to the field
     *
     * @var string
     */
    description: string
    /**
     * shows as a line (question) to the field
     *
     * @var string
     */
    line: string
    /**
     * Should the field be required
     *
     * @var boolean
     */
    required: boolean
}
