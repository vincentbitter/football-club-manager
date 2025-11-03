import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { SelectControl, PanelBody } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
	const { teamId } = attributes;

	const teams = useSelect(
		(select) =>
			select("core").getEntityRecords("postType", "fcmanager_team", {
				per_page: -1,
			}),
		[],
	);

	const preview_team_id = useSelect(
		(select) => {
			if (teamId) {
				return teamId;
			}
			if (select("core/editor").getCurrentPostType() === "fcmanager_team") {
				return select("core/editor").getCurrentPostId();
			}
			return "";
		},
		[teamId],
	);

	const matches = useSelect(
		(select) =>
			select("core")
				.getEntityRecords(
					"postType",
					"fcmanager_match",
					preview_team_id
						? {
								per_page: 20,
								meta_key: "_fcmanager_match_team",
								meta_value: preview_team_id,
								results: true,
						  }
						: { per_page: 20, results: true },
				)
				?.map((match) => ({
					id: match.id,
					date: match.meta._fcmanager_match_date,
					starttime: match.meta._fcmanager_match_starttime,
					team: match.meta._fcmanager_match_team,
					opponent: match.meta._fcmanager_match_opponent,
					away: match.meta._fcmanager_match_away == "1",
					goals_for: match.meta._fcmanager_match_goals_for,
					goals_against: match.meta._fcmanager_match_goals_against,
				})),
		[preview_team_id],
	);

	const options = [
		{ label: __("Select a team", "football-club-manager"), value: "" },
	].concat(
		teams?.map((team) => ({
			label: team.title.rendered,
			value: team.id,
		})) || [],
	);

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Team Settings", "football-club-manager")}
					initialOpen={true}
				>
					{!teams ? (
						<p>{__("Loading teams...", "football-club-manager")}</p>
					) : (
						<SelectControl
							label={__("Choose a team...", "football-club-manager")}
							value={teamId}
							options={options}
							onChange={(newId) => setAttributes({ teamId: parseInt(newId) })}
							help={__(
								"If you add the component to a team page, you don't need to set this field. It will automatically show the matches of the current team.",
								"football-club-manager",
							)}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<div class="fcmanager-team-results">
					<h2>{__("Results", "football-club-manager")}</h2>
					{!matches?.length ? (
						<p>
							{__("No matches found for this team.", "football-club-manager")}
						</p>
					) : (
						<table class="fcmanager-matches">
							<tbody>
								{matches?.map((match) => (
									<tr key={match.id}>
										<td class="fcmanager-match-date">{match.date}</td>
										<td class="fcmanager-match-time">{match.starttime}</td>
										<td class="fcmanager-match-hometeam">
											{match.away
												? match.opponent
												: teams?.find((t) => t.id == match.team)?.title
														.rendered}
										</td>
										<td class="fcmanager-match-homescore">
											{match.away ? match.goals_against : match.goals_for}
										</td>
										<td class="fcmanager-match-separator">-</td>
										<td class="fcmanager-match-awayscore">
											{match.away ? match.goals_for : match.goals_against}
										</td>
										<td class="fcmanager-match-awayteam">
											{match.away
												? teams?.find((t) => t.id == match.team)?.title.rendered
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
