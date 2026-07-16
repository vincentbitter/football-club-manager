import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, RangeControl, CheckboxControl } from "@wordpress/components";
import { useState } from "@wordpress/element";
import { useSelect } from "@wordpress/data";

export default function Edit({ attributes, setAttributes }) {
    const { columns, genders, ageCategories } = attributes;

    const blockProps = useBlockProps();

    const genderOptions = [
        { value: 'male', label: __('Male', 'football-club-manager') },
        { value: 'female', label: __('Female', 'football-club-manager') },
        { value: 'mixed', label: __('Mixed', 'football-club-manager') }
    ];

    const ageCategoryOptions = [
        { value: 'youth', label: __('Youth', 'football-club-manager') },
        { value: 'seniors', label: __('Seniors', 'football-club-manager') }
    ];

    const handleGenderChange = (gender, checked) => {
        if (checked) {
            setAttributes({ genders: [...genders, gender] });
        } else {
            setAttributes({ genders: genders.filter(g => g !== gender) });
        }
    };

    const handleAgeCategoryChange = (ageCategory, checked) => {
        if (checked) {
            setAttributes({ ageCategories: [...ageCategories, ageCategory] });
        } else {
            setAttributes({ ageCategories: ageCategories.filter(a => a !== ageCategory) });
        }
    };

    const all_teams = useSelect(
        (select) =>
            select("core").getEntityRecords("postType", "fcmanager_team", {
                per_page: -1,
            }),
        [],
    );

    const teams = useSelect(
        (select) => {
            const show_teams = all_teams?.filter((team) => {
                return (
                    genders.includes(team.meta["_fcmanager_team_gender"][0]) &&
                    ageCategories.includes(team.meta["_fcmanager_team_age_category"][0])
                );
            }) ?? Array.from({ length: 10 }).map((_, index) => ({ id: index, title: `Team ${index}` }));

            return show_teams.map((team) => ({
                id: team.id,
                name: team.title
            }))
        },
        [genders, ageCategories, all_teams],
    );

    const items_per_column = useSelect(
        (select) => teams ? teams.length / columns : 0,
        [teams, columns]);

    return (
        <>
            <InspectorControls>
                <PanelBody title={__("Display", "football-club-manager")}>
                    <RangeControl
                        label={__("Number of columns", "football-club-manager")}
                        value={columns}
                        min={1}
                        max={12}
                        onChange={(newValue) =>
                            setAttributes({ columns: parseInt(newValue) })
                        }
                    />
                </PanelBody>

                <PanelBody title={__("Show teams", "football-club-manager")}>
                    {genderOptions.map(option => (
                        <CheckboxControl
                            key={option.value}
                            label={option.label}
                            checked={genders.includes(option.value)}
                            onChange={(checked) => handleGenderChange(option.value, checked)}
                        />
                    ))}
                    {ageCategoryOptions.map(option => (
                        <CheckboxControl
                            key={option.value}
                            label={option.label}
                            checked={ageCategories.includes(option.value)}
                            onChange={(checked) => handleAgeCategoryChange(option.value, checked)}
                        />
                    ))}
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div class="fcmanager-teams-block">
                    <div class="wp-block-columns">
                        {Array.from({ length: columns }).map((_, index) => (
                            <div class="wp-block-column" key={index}>
                                <ul>
                                    {teams?.slice(Math.ceil(index * items_per_column), Math.ceil((index + 1) * items_per_column)).map((team) => (
                                        <li key={team.id}><a href="#">{team.name}</a></li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
}