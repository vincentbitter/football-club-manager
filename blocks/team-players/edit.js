import { __, sprintf } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { SelectControl, PanelBody } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
	const { teamId } = attributes;
	const pluginUrl = FootballClubManager.pluginUrl;

	const teams = useSelect(
		(select) =>
			select("core").getEntityRecords("postType", "fcmanager_team", { per_page: -1 }),
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

	const players = useSelect(
		(select) =>
			preview_team_id
				? select("core")
						.getEntityRecords("postType", "fcmanager_player", {
							per_page: -1,
							meta_key: "_fcmanager_player_team",
							meta_value: preview_team_id,
						})
						?.map((player) => ({
							id: player.id,
							name: player.title,
							photo: player.photo,
							all: player,
						}))
				: Array.from({ length: 11 }, (_, i) => ({
						id: i + 1,
						name: __("Player Name", "football-club-manager"),
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
								"If you add the component to a team page, you don't need to set this field. It will automatically show the players of the current team.",
								"football-club-manager",
							)}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<div class="fcmanager-team-players">
					<h2>
						{sprintf(
							__("Players of %s", "football-club-manager"),
							preview_team_id
								? teams?.find((team) => team.id === preview_team_id)?.title
										.rendered
								: __("Team", "football-club-manager"),
						)}
					</h2>
					{!players?.length ? (
						<p>
							{__("No players found for this team.", "football-club-manager")}
						</p>
					) : (
						<ul class="fcmanager-player-list">
							{players?.map((player) => (
								<li class="fcmanager-player-card" key={player.id}>
									<figure class="fcmanager-player-photo">
										{player.photo ? (
											<img src={player.photo} alt={player.name} />
										) : (
											<div
												class="fcmanager-placeholder"
												style={{
													WebkitMaskImage: `url("${pluginUrl}/assets/player.svg")`,
													maskImage: `url("${pluginUrl}/assets/player.svg")`,
												}}
											/>
										)}
									</figure>

									<strong>{player.name}</strong>
								</li>
							))}
						</ul>
					)}
				</div>
			</div>
		</>
	);
}
