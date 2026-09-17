import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { SelectControl, PanelBody, CheckboxControl } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
	const { teamId, showSubscribeButtons } = attributes;

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
							upcoming: true,
						}
						: { per_page: 20, upcoming: true },
				)
				?.map((match) => ({
					id: match.id,
					date: match.meta._fcmanager_match_date,
					starttime: match.meta._fcmanager_match_starttime,
					team: match.meta._fcmanager_match_team,
					opponent: match.meta._fcmanager_match_opponent,
					away: match.meta._fcmanager_match_away == "1",
					referee: match.meta._fcmanager_match_referee,
				})),
		[preview_team_id],
	);

	const options = [
		{ label: __("Select a team", "football-club-manager"), value: "" },
	].concat(
		teams?.map((team) => ({
			label: team.title,
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
					<CheckboxControl
						label={__("Show subscribe buttons", "football-club-manager")}
						checked={showSubscribeButtons}
						onChange={(checked) => setAttributes({ showSubscribeButtons: checked })}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<div class="fcmanager-team-schedule">
					<h2>{__("Upcoming matches", "football-club-manager")}</h2>
					{!matches?.length ? (
						<p>
							{__("No matches found for this team.", "football-club-manager")}
						</p>
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
								{matches?.map((match) => (
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
					{showSubscribeButtons &&
						<div class="fcmanager-calendar-buttons">
							<h3>{__("Add to your calendar", "football-club-manager")}</h3>
							<a class="wp-block-button__link" href="#">
								iPhone / iPad / Mac
							</a>
							<a class="wp-block-button__link" href="#">
								Google Calendar (Android)
							</a>
							<a class="wp-block-button__link" href="#">
								Windows / Outlook Desktop
							</a>
						</div >}
				</div >
			</div >
		</>
	);
}
