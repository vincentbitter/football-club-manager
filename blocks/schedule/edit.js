import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { useDebounce } from "@wordpress/compose";
import { useState, useEffect } from "@wordpress/element";
import { PanelBody, RangeControl } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
	const { numberOfItems, numberOfDays } = attributes;

	const [params, setParams] = useState({ numberOfDays, numberOfItems });
	const debouncedUpdate = useDebounce((days, items) => {
		setParams({ numberOfDays: days, numberOfItems: items });
	}, 300);

	useEffect(() => {
		debouncedUpdate(numberOfDays, numberOfItems);
	}, [numberOfDays, numberOfItems]);

	const teams = useSelect(
		(select) =>
			select("core").getEntityRecords("postType", "fcmanager_team", {
				per_page: -1,
			}),
		[],
	);

	const matches = useSelect(
		(select) =>
			select("core")
				.getEntityRecords("postType", "fcmanager_match", {
					per_page: params.numberOfItems,
					meta_key: "_fcmanager_match_date",
					meta_value: new Date(
						new Date().getTime() + params.numberOfDays * 24 * 60 * 60 * 1000,
					)
						.toISOString()
						.slice(0, 10),
					meta_compare: "<",
					meta_type: "DATE",
					upcoming: true,
				})
				?.map((match) => ({
					id: match.id,
					date: match.meta._fcmanager_match_date,
					starttime: match.meta._fcmanager_match_starttime,
					team: match.meta._fcmanager_match_team,
					opponent: match.meta._fcmanager_match_opponent,
					away: match.meta._fcmanager_match_away == "1",
				})),
		[params],
	);

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Query", "football-club-manager")}
					initialOpen={true}
				>
					<RangeControl
						label={__("Number of items", "football-club-manager")}
						value={numberOfItems}
						onChange={(newValue) =>
							setAttributes({ numberOfItems: parseInt(newValue) })
						}
						help={__(
							"Select the maximum number of matches to show.",
							"football-club-manager",
						)}
					/>
					<RangeControl
						label={__("Number of days", "football-club-manager")}
						value={numberOfDays}
						onChange={(newValue) =>
							setAttributes({ numberOfDays: parseInt(newValue) })
						}
						help={__(
							"Select the number of days to search for matches.",
							"football-club-manager",
						)}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<div class="fcmanager-schedule">
					<h2>{__("Upcoming matches", "football-club-manager")}</h2>
					{!matches?.length ? (
						<p>{__("No matches found.", "football-club-manager")}</p>
					) : (
						<table class="fcmanager-matches fcmanager-matches-schedule">
							<tbody>
								{matches
									?.filter((match) => teams?.find((t) => t.id == match.team))
									.map((match) => (
										<tr key={match.id}>
											<td class="fcmanager-match-date">{match.date}</td>
											<td class="fcmanager-match-time">{match.starttime}</td>
											<td class="fcmanager-match-hometeam">
												{match.away
													? match.opponent
													: teams?.find((t) => t.id == match.team)?.title
															.rendered}
											</td>
											<td class="fcmanager-match-separator">-</td>
											<td class="fcmanager-match-awayteam">
												{match.away
													? teams?.find((t) => t.id == match.team)?.title
															.rendered
													: match.opponent}
											</td>
										</tr>
									))}
							</tbody>
						</table>
					)}
				</div>
			</div>
		</>
	);
}
