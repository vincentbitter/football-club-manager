import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { useDebounce } from "@wordpress/compose";
import { useState, useEffect } from "@wordpress/element";
import { PanelBody, RangeControl, SelectControl } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
	const { numberOfItems, numberOfDays, homeAwayMatches } = attributes;

	const [params, setParams] = useState({ numberOfDays, numberOfItems, homeAwayMatches });
	const debouncedUpdate = useDebounce((days, items, homeAwayMatches) => {
		setParams({ numberOfDays: days, numberOfItems: items, homeAwayMatches: homeAwayMatches });
	}, 300);

	useEffect(() => {
		debouncedUpdate(numberOfDays, numberOfItems, homeAwayMatches);
	}, [numberOfDays, numberOfItems, homeAwayMatches]);

	const teams = useSelect(
		(select) =>
			select("core").getEntityRecords("postType", "fcmanager_team", {
				per_page: -1,
			}),
		[],
	);

	const referees = useSelect(
		(select) =>
			select("core").getEntityRecords("postType", "fcmanager_referee", {
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
					meta_compare: "lt",
					meta_type: "DATE",
					upcoming: true,
					homeOnly: params.homeAwayMatches == 'home',
					awayOnly: params.homeAwayMatches == 'away',
				})
				?.map((match) => ({
					id: match.id,
					date: match.meta._fcmanager_match_date,
					starttime: match.meta._fcmanager_match_starttime,
					team: match.meta._fcmanager_match_team,
					opponent: match.meta._fcmanager_match_opponent,
					away: match.meta._fcmanager_match_away == "1",
					referee: match.meta._fcmanager_match_referee,
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
					<SelectControl
						label={__("Home/Away matches", "football-club-manager")}
						value={homeAwayMatches}
						options={[
							{
								label: __("All matches", "football-club-manager"),
								value: "",
							},
							{
								label: __("Home matches", "football-club-manager"),
								value: "home",
							},
							{
								label: __("Away matches", "football-club-manager"),
								value: "away",
							},
						]}
						onChange={(newValue) =>
							setAttributes({ homeAwayMatches: newValue })
						}
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
							<thead>
								<tr>
									<th colspan="2">{__("Date/time", "football-club-manager")}</th>
									<th colspan="3">{__("Match", "football-club-manager")}</th>
									<th>{__("Referee", "football-club-manager")}</th>
								</tr>
							</thead>
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
													: teams?.find((t) => t.id == match.team)?.title}
											</td>
											<td class="fcmanager-match-separator">-</td>
											<td class="fcmanager-match-awayteam">
												{match.away
													? teams?.find((t) => t.id == match.team)?.title
													: match.opponent}
											</td>
											<td class="fcmanager-match-referee">
												{referees?.find(
													(r) => r.id == match.referee,
												)?.title || ""}
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
